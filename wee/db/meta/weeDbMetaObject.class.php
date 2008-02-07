<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
	Base class used to query meta informations about database objects.
*/

abstract class weeDbMetaObject implements ArrayAccess, Printable
{
	/**
		The database to query.
	*/

	protected $oDb;

	/**
		The database object informations.
	*/

	protected $aInfos;

	/**
		Initializes a new table schema.

		This class should NEVER be instantiated manually.
		An instance of this class should be returned by weeDbMeta
		or the other classes of the dbmeta component.

		@param	$oDb		The database to query.
		@param	$aInfos		The object informations.
	*/

	public function __construct(weeDatabase $oDb, array $aInfos)
	{
		fire($oDb == null, 'UnexpectedValueException',
			'$oDb is null.');

		// We can't use __CLASS__ or self without breaking inheritance.
		$aFields = call_user_func(array(get_class($this), 'getFields'));
		foreach ($aFields as $sField)
			fire(!array_key_exists($sField, $aInfos), 'UnexpectedValueException',
				'$aInfos[' . $sField . '] must be set.');

		$this->oDb		= $oDb;
		$this->aInfos	= $aInfos;
	}

	/**
		Returns the array of custom offsets reachable through ArrayAccess interface.
		By default, there is only one custom offset: "name".

		@return	array	The array of custom offsets.
	*/

	protected static function getCustomOffsets()
	{
		return array('name');
	}

	/**
		Returns the value of a custom offset.

		@param	$sOffset					The custom offset.
		@throw	UnexpectedValueException	The custom offset is invalid.
		@return mixed						The value associated with the custom offset.
	*/

	protected function getCustomOffset($sOffset)
	{
		switch ($sOffset)
		{
			case 'name':	return $this->name();
		}

		burn('UnexpectedValueException',
			"'$sOffset' is not a valid custom offset");
	}

	/**
		Returns the array of fields which need to be passed to the constructor of the class.

		@return	array	The array of fields.
	*/

	public static function getFields()
	{
		// We can't declare abstract static methods.
		burn('BadMethodCallException',
			'This method must be overriden in subclasses.');
	}

	/**
		Returns the array of fields used to order the objects in the SQL SELECT query.

		@return	array	The array of order fields.
	*/

	public static function getOrderFields()
	{
		// We can't declare abstract static methods.
		burn('BadMethodCallException',
			'This method must be overriden in subclasses.');
	}

	/**
		Returns the name of the information_schema table where the dbmeta objects
		are stored.

		@return	string	The table name.
	*/

	public static function getTable()
	{
		// We can't declare abstract static methods.
		burn('BadMethodCallException',
			'This method must be overriden in subclasses.');
	}

	/**
		Returns the name of the database object.

		@return	string	The name of the database object.
	*/

	abstract public function name();

	/**
		Returns whether a given offset is a valid offset for the ArrayAccess interface.

		It returns true if $sOffset is in in one of the arrays returned by getFields() and
		getCustomOffsets() methods.

		@see	http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($sOffset)
	{
		$aOffsets	= array_merge(
			call_user_func(array(get_class($this), 'getFields')),
			call_user_func(array(get_class($this), 'getCustomOffsets')));

		return in_array($sOffset, $aOffsets);
	}

	/**
		Returns the information associated with the given offset.

		The offset can be a SQL field returned by the getFields() method, or a custom
		offset returned by the getCustomFields(), these offsets are lazy constructed by the class
		on demand. They can be used to construct other meta objects.

		@see	http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
		@throw	UnexpectedValueException	The offset is invalid.
	*/

	public function offsetGet($sOffset)
	{
		fire(!$this->offsetExists($sOffset), 'UnexpectedValueException',
			"'$sOffset' is not a valid offset");
		
		if (!array_key_exists($sOffset, $this->aInfos))
			$this->aInfos[$sOffset] = $this->getCustomOffset($sOffset);

		return $this->aInfos[$sOffset];
	}

	/**
		Do NOT use it. Informations about database objects are read-only for the time being.
	*/

	public final function offsetSet($sOffset, $mValue)
	{
		burn('BadMethodCallException',
			'Informations about database objects are read-only.');
	}

	/**
		Do NOT use it. Informations about database objects are read-only for the time being.
	*/

	public final function offsetUnset($sOffset)
	{
		burn('BadMethodCallException',
			'Informations about database objects are read-only.');
	}

	/**
		Returns the string representation of the database object.

		@return	string	The string representation.
	*/

	public function toString()
	{
		return $this->name();
	}
}
