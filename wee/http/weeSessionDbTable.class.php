<?php

/*
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ALLOW_INCLUSION')) die;

/**
	Wrapper around $_SESSION for easier session management.
	The session data will be stored in a database instead of on the filesystem.
*/

class weeSessionDbTable extends weeSession
{
	/**
		The database associated with this session.
	*/

	protected $oDatabase;

	/**
		Whether the session already exists in the table.

		This property is used to determine which of INSERT or UPDATE
		statement we need to use when writing the session in the database.
		PHP will always try to read the session before writing it back in
		the database, allowing us to know whether the row exists.
	*/

	protected $bExists = true;

	/**
		Name of the session. Defaults to PHPSESSID.
		Defined internally by PHP's session storage handler.
	*/

	protected $sName;

	/**
		Path where the session should be stored.
		Defined internally by PHP's session storage handler.
	*/

	protected $sSavePath;

	/**
		Initialize the session storage handler.

		Along with the parameters defined by weeSession::construct, another
		parameter "table" identify which table should be used to store sessions.

		@param $aParams A list of parameters to configure the session class.
		@see weeSession::__construct
	*/

	public function __construct($aParams = array())
	{
		// We are required to include this class now in case it is needed
		// when this object gets destroyed at the end of the script.
		// Autoload do not work if the script is terminating, resulting in a fatal error.
		class_exists('DatabaseException');

		session_set_save_handler(
			array($this, 'storageOpen'),
			array($this, 'storageClose'),
			array($this, 'storageRead'),
			array($this, 'storageWrite'),
			array($this, 'storageDestroy'),
			array($this, 'storageGarbageCollector')
		);

		empty($aParams['db']) || $aParams['db'] instanceof weeDatabase or burn('InvalidArgumentException',
			_WT('The optional "db" parameter must be an instance of weeDatabase.'));
		isset($aParams['table']) or burn('InvalidArgumentException',
			_WT('The required "table" parameter is missing.'));

		if (!empty($aParams['db']))
			$this->setDb($aParams['db']);

		parent::__construct($aParams);
	}

	/**
		Close the session storage handler before destroying the object.

		According to the PHP manual:
			As of PHP 5.0.5 the write and close handlers are called after object
			destruction and therefore cannot use objects or throw exceptions.
			The object destructors can however use sessions. It is possible to call
			session_write_close() from the destructor to solve this chicken and egg problem.
	*/

	public function __destruct()
	{
		session_write_close();
	}

	/**
		Returns the database associated to this model.

		@return	weeDatabase				The database associated to this model.
		@throw	IllegalStateException	No database has been associated to this model.
	*/

	public function getDb()
	{
		$this->oDatabase !== null or is_callable('weeApp')
			or burn('IllegalStateException',
				_WT('No database has been associated with this session.'));

		return $this->oDatabase === null ? weeApp()->db : $this->oDatabase;
	}

	/**
		Associate a database to this model.

		@param	$oDb	The database instance to associate to this model.
		@return	$this	Used to chain methods.
	*/

	public function setDb(weeDatabase $oDb)
	{
		$this->oDatabase = $oDb;
		return $this;
	}

	/**
		Close the session storage.
		Used internally by PHP's session storage handler.

		@return bool Whether the storage was successfully closed.
	*/

	public function storageClose()
	{
		return true;
	}

	/**
		Delete a session from the storage.
		Used internally by PHP's session storage handler.

		@param $sSessionId The identifier of the session to be deleted.
		@return bool Whether the session has been deleted.
	*/

	public function storageDestroy($sSessionId)
	{
		try {
			$this->getDb()->query('
				DELETE FROM ' . $this->getDb()->escapeIdent($this->aParams['table']) . '
					WHERE	session_id=:session_id
						AND	session_path=:session_path
						AND	session_name=:session_name
			', array(
				'session_id'	=> $sSessionId,
				'session_path'	=> $this->sSavePath,
				'session_name'	=> $this->sName,
			));

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
		Run the session garbage collector.
		Used internally by PHP's session storage handler.

		@param $iMaxLifeTime The maximum time passed since the last access to the session.
		@return bool Whether the garbage collector completed successfully.
	*/

	public function storageGarbageCollector($iMaxLifeTime)
	{
		$this->getDb()->query('
				DELETE FROM ' . $this->getDb()->escapeIdent($this->aParams['table']) . '
					WHERE	session_time<:time
						AND	session_path=:session_path
						AND	session_name=:session_name
			', array(
				'time'			=> time() - $iMaxLifeTime,
				'session_path'	=> $this->sSavePath,
				'session_name'	=> $this->sName,
			));

		return true;
	}

	/**
		Open the session storage.
		Used internally by PHP's session storage handler.

		@param $sSessionSavePath Path where the session should be stored.
		@param $sSessionName Name of the session.
		@return Whether the storage was open successfully.
	*/

	public function storageOpen($sSessionSavePath, $sSessionName)
	{
		$this->sSavePath = $sSessionSavePath;
		$this->sName = $sSessionName;

		return true;
	}

	/**
		Read a session from the storage.
		Used internally by PHP's session storage handler.

		@param $sSessionId The identifier of the session requested.
		@return bool The session's data.
	*/

	public function storageRead($sSessionId)
	{
		try {
			return $this->getDb()->queryValue('
				SELECT session_data
					FROM ' . $this->getDb()->escapeIdent($this->aParams['table']) . '
					WHERE	session_id=:session_id
						AND	session_path=:session_path
						AND	session_name=:session_name
					LIMIT 1
			', array(
				'session_id'	=> $sSessionId,
				'session_path'	=> $this->sSavePath,
				'session_name'	=> $this->sName,
			));
		} catch (Exception $e) {
			$this->bExists = false;
			return '';
		}
	}

	/**
		Store a session.
		Used internally by PHP's session storage handler.

		@param $sSessionId The identifier of the session to be stored.
		@param $sSessionData The data of the session.
		@return mixed The number of bytes written or false on failure.
	*/

	public function storageWrite($sSessionId, $sSessionData)
	{
		try {
			$aArgs = array(
				'session_id'	=> $sSessionId,
				'session_path'	=> $this->sSavePath,
				'session_name'	=> $this->sName,
				'session_time'	=> time(),
				'session_data'	=> $sSessionData,
			);

			if ($this->bExists) {
				$this->getDb()->query('
					UPDATE ' . $this->getDb()->escapeIdent($this->aParams['table']) . '
						SET session_time=:session_time,
							session_data=:session_data
						WHERE	session_id=:session_id
							AND	session_path=:session_path
							AND	session_name=:session_name
				', $aArgs);
			} else {
				$this->getDb()->query('
					INSERT INTO ' . $this->getDb()->escapeIdent($this->aParams['table']) . '
						(session_id, session_path, session_name, session_time, session_data)
						VALUES (:session_id, :session_path, :session_name, :session_time, :session_data)
				', $aArgs);
			}

			return strlen($sSessionData);
		} catch (Exception $e) {
			return false;
		}
	}
}
