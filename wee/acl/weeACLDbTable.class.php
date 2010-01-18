<?php

/**
	Access Control List driver for data stored in a database.

	The ACL is stored in two different tables in the database. Those databases are linked to each other
	through a role. The role is completely transparent and automatically defined. The two tables represent
	a subject-role and role-permission relationships, effectively storing the subjects' permissions while
	avoiding to take too much storage space in case of duplicate permissions for different subjects.
*/

class weeACLDbTable
{
	/**
		Contains the database information used to store the ACLs.
	*/

	protected $aParams;

	/**
		Creates a new ACL driver.

		Parameters:
			* db:				The weeDatabase object to be used. Defaults to weeApp()->db.
			* sr_table:			The table containing the subject-role relationship.
			* rp_table:			The table containing the role-permissions relationship.
			* subject_fields:	Field names forming the primary key identifying subjects. Defaults to 'user_id'.
			* role_field:		Field name for the role ID. Usually an integer. Defaults to 'role_id'.
			* operation_field:	Field name for the operation. Usually a string. Defaults to 'perm_operation'.
			* resource_fields:	Field names to define the resources. Defaults to array('perm_resource').

		@param $aParams List of parameters to use for the database.
	*/

	public function __construct($aParams)
	{
		if (empty($aParams['db']))
			$aParams['db'] = weeApp()->db;

		is_object($aParams['db']) && $aParams['db'] instanceof weeDatabase or burn('InvalidArgumentException',
			sprintf(_WT('Parameter "%s" must be an instance of %s.'), 'db', 'weeDatabase'));

		empty($aParams['sr_table']) and burn('InvalidArgumentException',
			_WT('You must provide a parameter "sr_table" containing the name of the subject-role table in your database.'));
		empty($aParams['rp_table']) and burn('InvalidArgumentException',
			_WT('You must provide a parameter "rp_table" containing the name of the role-permission table in your database.'));

		if (empty($aParams['subject_fields']))
			$aParams['subject_fields'] = 'user_id';
		if (empty($aParams['role_field']))
			$aParams['role_field'] = 'role_id';
		if (empty($aParams['operation_field']))
			$aParams['operation_field'] = 'perm_operation';

		if (empty($aParams['resource_fields']))
			$aParams['resource_fields'] = array('perm_resource');
		// We also accept the resource_fields parameter as a string of fields delimited by a coma
		elseif (is_string($aParams['resource_fields']))
			$aParams['resource_fields'] = explode(',', $aParams['resource_fields']);

		$this->aParams = $aParams;
	}

	/**
		Adds a new permission to a subject.
		The subject is given the permission to do the given operation on the given resource.

		@param $mSubject The subject affected. Either an array or a primitive type.
		@param $sOperation The operation allowed. Either null (means ANY) or a string.
		@param $mResource The resource the subject can affect. Either null (means ANY) or an array of names/values. A missing value also means ANY for this field.
	*/

	public function add($mSubject, $sOperation = null, $mResource = null)
	{
		$oDb = $this->getDb();

		// First we check if a role already exists as we don't want dupes

		try {
			$iRole = $this->fetchRole($oDb, $sOperation, $mResource);
		} catch (Exception $e) {
			// The role doesn't exist, let's insert it!

			$sSQL = 'INSERT INTO ' . $oDb->escapeIdent($this->aParams['rp_table'])
				. ' (' . $oDb->escapeIdent($this->aParams['operation_field']);

			foreach ($this->aParams['resource_fields'] as $sField)
				$sSQL .= ',' . $oDb->escapeIdent($sField);

			$sSQL .= ') VALUES (' . $oDb->escape($sOperation);

			foreach ($this->aParams['resource_fields'] as $sField) {
				if (isset($mResource[$sField]))
					$sSQL .= ',' . $oDb->escape($mResource[$sField]);
				else
					$sSQL .= ',NULL';
			}

			$oDb->query($sSQL . ')');
			$iRole = $oDb->getPKId();
		}

		// Then we give the subject its new role

		if (!is_array($this->aParams['subject_fields']))
			$mSubject = array($this->aParams['subject_fields'] => $mSubject);

		$sSQL = 'INSERT INTO ' . $oDb->escapeIdent($this->aParams['sr_table'])
				. ' (' . $oDb->escapeIdent($this->aParams['role_field']);

		foreach ($mSubject as $sField => $mValue)
			$sSQL .= ',' . $oDb->escapeIdent($sField);

		$sSQL .= ') VALUES (' . $oDb->escape($iRole);

		foreach ($mSubject as $mValue)
			$sSQL .= ',' . $oDb->escape($mValue);

		$oDb->query($sSQL . ')');
	}

	/**
		Deletes one or permission.

		To delete all the permissions from a single subject, just give the subject parameter.
		Be careful in what you ask to delete, as all permissions matching the parameters will be deleted.

		@param $mSubject The subject affected. Either an array or a primitive type.
		@param $sOperation The operation allowed. Either null (means ALL) or a string.
		@param $mResource The resource the subject can affect. Either null (means ALL) or an array of names/values. A missing value also means ALL for this field.
	*/

	public function delete($mSubject, $sOperation = null, $mResource = null)
	{
		$oDb = $this->getDb();

		// First we fetch the role used in case we have to also delete it

		$iRole = $this->fetchRole($oDb, $sOperation, $mResource);

		// Then we delete the subject-role relationship
		// and also the role-perm relationship if it's not used anymore

		$oDb->query('
			DELETE FROM ' . $oDb->escapeIdent($this->aParams['sr_table']) . ' AS sr
				WHERE ' . $this->subjectToSQL($oDb, $mSubject) . '
				AND ' . $oDb->escapeIdent($this->aParams['role_field']) . '=?
		', $iRole);

		$iCount = $oDb->queryValue('
			SELECT COUNT(*) FROM ' . $oDb->escapeIdent($this->aParams['sr_table']) . '
				WHERE ' . $oDb->escapeIdent($this->aParams['role_field']) . '=?
		', $iRole);

		if ($iCount == 0) {
			$oDb->query('
				DELETE FROM ' . $oDb->escapeIdent($this->aParams['rp_table']) . '
					WHERE ' . $oDb->escapeIdent($this->aParams['role_field']) . '=?
			', $iRole);
		}
	}

	/**
		Retrieves all the permissions associated with the given subject.

		@param $mSubject The subject queried. Either an array or a primitive type.
	*/

	public function fetch($mSubject)
	{
		$oDb = $this->getDb();

		return $oDb->query('
			SELECT *
				FROM ' . $oDb->escapeIdent($this->aParams['sr_table']) . ' AS sr
				LEFT OUTER JOIN ' . $oDb->escapeIdent($this->aParams['rp_table']) . ' AS rp
				ON (sr.' . $oDb->escapeIdent($this->aParams['role_field']) . '=rp.' . $oDb->escapeIdent($this->aParams['role_field']) . ')
				WHERE ' . $this->subjectToSQL($oDb, $mSubject)
		)->fetchAll();
	}

	/**
		Retrieve a specific role identifier from the database based on the operation and the resource.

		@param $oDb The database to be used.
		@param $sOperation The operation allowed.
		@param $mResource The resource the subject can affect.
	*/

	protected function fetchRole($oDb, $sOperation, $mResource)
	{
		// We want to fetch a specific role, therefore we can't use permissionToSQL
		// since it'll allow 'ANY' values that can return more than one result.
		// The code here is rather similar to that method, though, yet fairly different.

		if (is_null($sOperation))
			$sSQL = 'rp.' . $oDb->escapeIdent($this->aParams['operation_field']) . ' IS NULL';
		else {
			is_string($sOperation) or burn('InvalidArgumentException',
				_WT('The argument $sOperation must be either null or a string.'));

			$sSQL = 'rp.' . $oDb->escapeIdent($this->aParams['operation_field']) . '=' . $oDb->escape($sOperation);
		}

		if (is_null($mResource))
			$mResource = array();

		is_array($mResource) or burn('InvalidArgumentException',
			_WT('The argument $mResource must be either null or an array.'));

		foreach ($this->aParams['resource_fields'] as $sField) {
			if (isset($mResource[$sField]))
				$sSQL .= ' AND rp.' . $oDb->escapeIdent($sField) . '=' . $oDb->escape($mResource[$sField]);
			else
				$sSQL .= ' AND rp.' . $oDb->escapeIdent($sField) . ' IS NULL';
		}

		// End of WHERE-clause build.

		return $oDb->queryValue('
			SELECT ' . $oDb->escapeIdent($this->aParams['role_field']) . '
				FROM ' . $oDb->escapeIdent($this->aParams['rp_table']) . ' AS rp
				WHERE ' . $sSQL
		);
	}

	/**
		Returns the database associated to this ACL driver.

		@return weeDatabase The database associated to this ACL driver.
	*/

	public function getDb()
	{
		return $this->aParams['db'];
	}

	/**
		Returns a boolean defining whether the given subject is allowed to perform the given operation on the given resource.

		@param $mSubject The subject affected. Either an array or a primitive type.
		@param $sOperation The operation allowed. Either null (means ANY) or a string.
		@param $mResource The resource the subject can affect. Either null (means ANY) or an array of names/values. A missing value also means ANY for this field.
	*/

	public function isAllowed($mSubject, $sOperation = null, $mResource = null)
	{
		$oDb = $this->getDb();

		return 0 < $oDb->queryValue('
			SELECT COUNT(*)
				FROM ' . $oDb->escapeIdent($this->aParams['sr_table']) . ' AS sr
				LEFT OUTER JOIN ' . $oDb->escapeIdent($this->aParams['rp_table']) . ' AS rp
				ON (sr.' . $oDb->escapeIdent($this->aParams['role_field']) . '=rp.' . $oDb->escapeIdent($this->aParams['role_field']) . ')
				WHERE ' . $this->subjectToSQL($oDb, $mSubject) . '
				  AND ' . $this->permissionToSQL($oDb, $sOperation, $mResource)
		);
	}

	/**
		Translates the subject value(s) into SQL for use in a WHERE clause.

		@param $oDb The database to be used.
		@param $mSubject The subject affected.
	*/

	protected function subjectToSQL($oDb, $mSubject)
	{
		if (!is_array($this->aParams['subject_fields']))
			return 'sr.' . $oDb->escapeIdent($this->aParams['subject_fields']) . '=' . $oDb->escape($mSubject);

		$sSQL = '(TRUE';
		foreach ($this->aParams['subject_fields'] as $sField)
			$sSQL .= ' AND sr.' . $oDb->escapeIdent($sField) . '=' . $oDb->escape($mSubject);
		return $sSQL . ')';
	}

	/**
		Translates the operation and the resource values into SQL for use in a WHERE clause.

		@param $oDb The database to be used.
		@param $sOperation The operation allowed.
		@param $mResource The resource the subject can affect.
	*/

	protected function permissionToSQL($oDb, $sOperation, $mResource)
	{
		$sSQL = '(TRUE';

		if (is_null($sOperation))
			$sSQL .= ' AND rp.' . $oDb->escapeIdent($this->aParams['operation_field']) . ' IS NULL';
		else {
			is_string($sOperation) or burn('InvalidArgumentException',
				_WT('The argument $sOperation must be either null or a string.'));

			$sSQL .= ' AND (rp.' . $oDb->escapeIdent($this->aParams['operation_field']) . ' IS NULL OR rp.'
				. $oDb->escapeIdent($this->aParams['operation_field']) . '=' . $oDb->escape($sOperation) . ')';
		}

		if (is_null($mResource))
			$mResource = array();

		is_array($mResource) or burn('InvalidArgumentException',
			_WT('The argument $mResource must be either null or an array.'));

		foreach ($this->aParams['resource_fields'] as $sField) {
			$sSQL .= ' AND (rp.' . $oDb->escapeIdent($sField) . ' IS NULL';
			if (isset($mResource[$sField]))
				$sSQL .= ' OR rp.' . $oDb->escapeIdent($sField) . '=' . $oDb->escape($mResource[$sField]);
			$sSQL .= ')';
		}

		return $sSQL . ')';
	}
}
