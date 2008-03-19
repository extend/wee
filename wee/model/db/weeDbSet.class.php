<?php

/**
	Base class for defining a set of rows for a database table.
*/

abstract class weeDbSet extends weeSet
{
	/**
		The database this set is associated to.
		Defaults to weeApp()->db.
	*/

	protected $oDatabase;

	/**
		Returns the database associated to this set.

		@return weeDatabase The database associated to this set.
	*/

	public function getDb()
	{
		fire(empty($this->oDatabase) && !is_callable('weeApp'), 'IllegalStateException',
			'No database has been associated to this set.');

		return (empty($this->oDatabase) ? weeApp()->db : $this->oDatabase);
	}

	/**
		Associate a database to this set.

		@param $oDb weeDatabase The database instance to associate to this set.
		@return $this
	*/

	public function setDb($oDb)
	{
		$this->oDatabase = $oDb;
		return $this;
	}
}
