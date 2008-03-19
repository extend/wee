<?php

/**
	Base class for defining a model for a database table.
*/

abstract class weeDbModel extends weeModel
{
	/**
		The database this model is associated to.
		Defaults to weeApp()->db.
	*/

	protected $oDatabase;

	/**
		Returns the database associated to this model.

		@return weeDatabase The database associated to this model.
	*/

	public function getDb()
	{
		fire(empty($this->oDatabase) && !is_callable('weeApp'), 'IllegalStateException',
			'No database has been associated to this model.');

		return (empty($this->oDatabase) ? weeApp()->db : $this->oDatabase);
	}

	/**
		Associate a database to this model.

		@param $oDb weeDatabase The database instance to associate to this model.
		@return $this
	*/

	public function setDb($oDb)
	{
		$this->oDatabase = $oDb;
		return $this;
	}
}
