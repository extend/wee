<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	Scaffolding for database elements.

	To use it, simply extend it and define the $sModel property to the name of the weeDbModelScaffold class,
	and the $sTableName to the name of the table in the database represented by this set.

	@todo automatically fetch reference tables data
*/

abstract class weeDbSetScaffold extends weeDbSet implements Countable
{
	/**
		ORDER BY part of the SELECT queries.
		Can be defined here or by using the orderBy method.
	*/

	protected $sOrderBy;

	/**
		Name of the table in the database represented by this set.
	*/

	protected $sTableName;

	/**
		Valid criteria operators for use with the search methods.
	*/

	protected $aValidCriteriaOperators = array(
		'=', '>', '>=', '<', '<=', '!=',
		'LIKE', 'NOT LIKE',
		'IS NULL', 'IS NOT NULL',
		'IS TRUE', 'IS NOT TRUE',
		'IS FALSE', 'IS NOT FALSE',
		'IS UNKNOWN', 'IS NOT UNKNOWN',
		'IN', 'NOT IN', 'ANY', 'ALL',
	);

	/**
		Valid modifiers for use with the orderBy method.
	*/

	protected $aValidOrderByModifiers = array(
		'ASC', 'DESC',
	);

	/**
		Count the number of rows in the table.

		@return integer The number of rows in the table.
	*/

	public function count()
	{
		$aMeta = $this->getMeta();
		return $this->queryValue('SELECT COUNT(*) FROM ' . $aMeta['table']);
	}

	/**
		Delete a row (identified by its primary key) from the table.

		When the primary key is on only one column, simply pass the value of this column.
		Otherwise, you need to pass an associative array with all the values.

		@param $mPrimaryKey The primary key data. Can be either a scalar value or an associative array.
	*/

	public function delete($mPrimaryKey)
	{
		$oDb = $this->getDb();
		$aMeta = $this->getMeta();
		$mPrimaryKey = $this->filterPrimaryKey($mPrimaryKey, $aMeta);

		$sQuery = 'DELETE FROM ' . $aMeta['table'] . ' WHERE TRUE';
		foreach ($aMeta['primary'] as $sName)
			$sQuery .= ' AND ' . $oDb->escapeIdent($sName) . '=' . $oDb->escape($mPrimaryKey[$sName]);

		$this->query($sQuery);
	}

	/**
		Fetch a row (identified by its primary key) from the table.

		When the primary key is on only one column, simply pass the value of this column.
		Otherwise, you need to pass an associative array with all the values.

		@param $mPrimaryKey The primary key data. Can be either a scalar value or an associative array.
		@return object An instance of the model of this set.
	*/

	public function fetch($mPrimaryKey)
	{
		$oDb = $this->getDb();
		$aMeta = $this->getMeta();
		$mPrimaryKey = $this->filterPrimaryKey($mPrimaryKey, $aMeta);

		$sQuery = 'SELECT * FROM ' . $aMeta['table'] . ' WHERE TRUE';
		foreach ($aMeta['primary'] as $sName)
			$sQuery .= ' AND ' . $oDb->escapeIdent($sName) . '=' . $oDb->escape($mPrimaryKey[$sName]);

		return $this->queryRow($sQuery . ' LIMIT 1');
	}

	/**
		Alias of fetchSubset with default values. Fetch all rows in the table.
	*/

	public function fetchAll()
	{
		return $this->fetchSubset();
	}

	/**
		Fetch a subset of the rows from the table.

		@param $iOffset Start fetching from this offset.
		@param $iCount The number of rows to fetch.
		@return mixed An instance of weeDatabaseResult.
		@throw InvalidArgumentException $iOffset and $iCount must be integers.
	*/

	public function fetchSubset($iOffset = 0, $iCount = 0)
	{
		is_int($iOffset) or burn('InvalidArgumentException', _WT('$iOffset must be an integer.'));
		is_int($iCount) or burn('InvalidArgumentException', _WT('$iCount must be an integer.'));

		$aMeta = $this->getMeta();

		$sQuery = 'SELECT * FROM ' . $aMeta['table'] . ' WHERE TRUE';
		if (!empty($this->sOrderBy))
			$sQuery .= ' ORDER BY ' . $this->sOrderBy;
		$sQuery .= empty($iCount) ? '' : ' LIMIT ' . $iCount;
		$sQuery .= ' OFFSET ' . $iOffset;

		return $this->query($sQuery);
	}

	/**
		Check and prepare the primary key for use in the delete or fetch methods.

		@param $mPrimaryKey The primary key to prepare.
		@param $aMeta The metadata for this primary key's table.
		@return array The primary key ready to be used.
		@throw InvalidArgumentException The primary key value is empty or missing columns.
	*/

	protected function filterPrimaryKey($mPrimaryKey, $aMeta)
	{
		empty($mPrimaryKey) and burn('InvalidArgumentException', _WT('The primary key value must not be empty.'));

		if (!is_array($mPrimaryKey))
			$mPrimaryKey = array($aMeta['primary'][0] => $mPrimaryKey);

		$aDiff = array_diff($aMeta['primary'], array_keys($mPrimaryKey));
		empty($aDiff) or burn('InvalidArgumentException', _WT('The primary key value is incomplete.'));

		return $mPrimaryKey;
	}

	/**
		Get the metadata for the table associated with the set.

		The metadata returned contains information about:
		- table:	The full table name, properly quoted.
		- columns:	An array of all the columns names.
		- primary:	An array of all the primary key columns names.

		@return array The metadata for the table associated with the set.
	*/

	public function getMeta()
	{
		static $aMeta = array();

		if (empty($aMeta)) {
			$oTable = $this->getDb()->meta()->table($this->sTableName);
			$aMeta = array(
				'table'		=> $oTable->quotedName(),
				'columns'	=> $oTable->columnsNames(),
				'primary'	=> $oTable->primaryKey()->columns(),
			);
		}

		return $aMeta;
	}

	/**
		Insert a new row in the database, and return the model object for the inserted row.

		The model returned do not contain any other value that could be assigned to the
		row when doing the INSERT operation. If you need that kind of behavior, because
		you have a table with a sequenced primary key for example, you need to extend this
		method to retrieve the value. This usually means using $this->getDb()->getPKId().

		@return The model for the inserted row.
		@throw InvalidArgumentException The data given was empty.
	*/

	public function insert($aData)
	{
		empty($aData) and burn('InvalidArgumentException', _WT('$aData must not be empty.'));

		$oDb = $this->getDb();
		$aMeta = $this->getMeta();

		$sQueryNames = '';
		$sQueryValues = '';
		foreach ($aData as $sName => $mValue)
			if (in_array($sName, $aMeta['columns'])) {
				$sQueryNames .= $oDb->escapeIdent($sName) . ', ';
				$sQueryValues .= $oDb->escape($aData[$sName]) . ', ';
			}

		$sQuery = 'INSERT INTO ' . $aMeta['table'] . ' (' . substr($sQueryNames, 0, -2) . ') VALUES (' . substr($sQueryValues, 0, -2) . ')';
		$this->query($sQuery);

		return new $this->sModel($aData);
	}

	/**
		Defines the order of the rows returned by the fetchAll, fetchSubset and search operations.

		The parameter can be either a scalar value (for example, the name of the column to use for sorting)
		or an associative array. When providing an array, you can give as many sort options as you want,
		using the key as the field name and the value as the sort operation requested, which can be either
		ASC or DESC currently. If only the value is provided without a key, it is used as the field name
		and the order will default to ASC.

		@param $mOrderBy The order in which the rows should be sorted.
		@return $this
		@throw The ORDER BY modifier is not in the list of valid modifiers.
	*/

	public function orderBy($mOrderBy = array())
	{
		$oDb = $this->getDb();

		if (!is_array($mOrderBy))
			$this->sOrderBy = $oDb->escapeIdent($mOrderBy);
		else {
			$this->sOrderBy = '';

			foreach ($mOrderBy as $mName => $mValue) {
				if (is_int($mName))
					$this->sOrderBy .= ', ' . $oDb->escapeIdent($mValue);
				else {
					in_array($mValue, $this->aValidOrderByModifiers)
						or burn('InvalidArgumentException', _WT(sprintf('The ORDER BY modifier requested, %s, do not exist.', $mValue)));

					$this->sOrderBy .= ', ' . $oDb->escapeIdent($mName) . ' ' . $mValue;
				}
			}

			$this->sOrderBy = substr($this->sOrderBy, 2);
		}

		return $this;
	}

	/**
		Search rows matching the given criteria.

		The criteria must be an associative array with the keys being the field names
		and the values the operation to perform. The operation can be either a single
		value (the name of the operation), or an array containing the name of the operation
		along with one or more values. Here is the representation of all the possible
		forms of a single criteria:

			$aCriteria = array(
				'field_1' => 'operation',
				'field_2' => array('operation'),
				'field_3' => array('operation', 'value'),
				'field_4' => array('operation', 'value', 'more values', ...),
			);

		@param $aCriteria The criteria to search for.
		@param $iOffset Start fetching from this offset.
		@param $iCount The number of rows to fetch.
		@return mixed An instance of weeDatabaseResult.
		@throw InvalidArgumentException $iOffset and $iCount must be integers.
	*/

	public function search($aCriteria, $iOffset = 0, $iCount = 0)
	{
		is_int($iOffset) or burn('InvalidArgumentException', _WT('$iOffset must be an integer.'));
		is_int($iCount) or burn('InvalidArgumentException', _WT('$iCount must be an integer.'));

		$aMeta = $this->getMeta();

		$sQuery = 'SELECT * FROM ' . $aMeta['table'] . ' WHERE ' . $this->searchBuildWhere($aCriteria);
		if (!empty($this->sOrderBy))
			$sQuery .= ' ORDER BY ' . $this->sOrderBy;
		$sQuery .= empty($iCount) ? '' : ' LIMIT ' . $iCount;
		$sQuery .= ' OFFSET ' . $iOffset;

		return $this->query($sQuery);
	}

	/**
		Build the body of the WHERE clause based on the given criteria.

		The criteria must be an associative array with the keys being the field names
		and the values the operation to perform. The operation can be either a single
		value (the name of the operation), or an array containing the name of the operation
		along with one or more values. Here is the representation of all the possible
		forms of a single criteria:

			$aCriteria = array(
				'field_1' => 'operation',
				'field_2' => array('operation'),
				'field_3' => array('operation', 'value'),
				'field_4' => array('operation', 'value', 'more values', ...),
			);

		@param $aCriteria The criteria to search for.
		@return string The body of the WHERE clause built according to the criteria.
		@throw InvalidArgumentException The criteria operation is not in the list of valid operations.
	*/

	protected function searchBuildWhere($aCriteria)
	{
		$oDb = $this->getDb();
		$sWhere = 'TRUE';

		foreach ($aCriteria as $sField => $mOperation) {
			$sWhere .= ' AND ' . $oDb->escapeIdent($sField) . ' ';

			if (is_array($mOperation)) {
				in_array($mOperation[0], $this->aValidCriteriaOperators) or burn('InvalidArgumentException',
					_WT(sprintf('The criteria operation requested, "%s", do not exist.', $mOperation[0])));

				$sWhere .= $mOperation[0] . ' ';

				$iCount = count($mOperation);
				if ($iCount == 2)
					$sWhere .= $oDb->escape($mOperation[1]);
				elseif ($iCount > 2) {
					$sWhere .= '(';
					for ($i = 1; $i < $iCount; $i++)
						$sWhere .= $oDb->escape($mOperation[$i]) . ',';
					$sWhere = substr($sWhere, 0, -1) . ')';
				}
			} else {
				in_array($mOperation, $this->aValidCriteriaOperators) or burn('InvalidArgumentException',
					_WT(sprintf('The criteria operation requested, "%s", do not exist.', $mOperation)));

				$sWhere .= $mOperation;
			}
		}

		return $sWhere;
	}

	/**
		Return the total count of rows returned by a search on the given criteria.

		The criteria must be an associative array with the keys being the field names
		and the values the operation to perform. The operation can be either a single
		value (the name of the operation), or an array containing the name of the operation
		along with one or more values. Here is the representation of all the possible
		forms of a single criteria:

			$aCriteria = array(
				'field_1' => 'operation',
				'field_2' => array('operation'),
				'field_3' => array('operation', 'value'),
				'field_4' => array('operation', 'value', 'more values', ...),
			);

		@param $aCriteria The criteria to search for.
		@return integer The number of rows returned by a search using this criteria.
	*/

	public function searchCount($aCriteria)
	{
		$aMeta = $this->getMeta();
		return $this->queryValue('SELECT COUNT(*) FROM ' . $aMeta['table'] . ' WHERE ' . $this->searchBuildWhere($aCriteria));
	}
}
