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
	Scaffolding for database elements.

	To use it, simply extend it and define the $sModel property to the name of the weeDbModelScaffold class,
	and the $sTableName to the name of the table in the database represented by this set.
*/

abstract class weeDbSetScaffold extends weeDbSet implements Countable
{
	/**
		Defines the type of JOIN to do when joining reference tables.
	*/

	protected $sJoinType = 'LEFT OUTER JOIN';

	/**
		The metadata for the table associated with this set.

		The metadata contains information about:
		* table:	The full table name, properly quoted.
		* columns:	An array of all the columns names.
		* primary:	An array of all the primary key columns names.
	*/

	protected $aMeta;

	/**
		ORDER BY part of the SELECT queries.
		Can be defined here or by using the orderBy method.
	*/

	protected $sOrderBy;

	/**
		Reference tables to fetch by doing a JOIN in SELECT queries.
	*/

	protected $aRefSets = array();

	/**
		Name of the table in the database represented by this set.
	*/

	protected $sTableName;

	/**
		The criteria used to determine the subset.

		@see weeDbSetScaffold::__construct
	*/

	protected $aSubsetCriteria;

	/**
		Valid criteria logical operators for use when defining subsets.
	*/

	protected $aValidCriteriaLogicalOperators = array(
		'OR',		// union of A and B
		'AND',		// intersection of A and B
		'AND NOT',	// complement of B in A (in other words: A - B)
		'XOR',		// symmetric difference of A and B
	);

	/**
		Valid criteria operators for use when defining subsets.
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
		Filter rows according to the given criteria. This effectively creates a subset.

		Note that delete, fetch, insert and update operations do not try to restrict
		you to the subset. The result of these operations may or may not affect the
		subset. By convention, you shouldn't use those methods on subset objects.

		The criteria must be an associative array with the keys being the field names
		and the values the operation to perform. The operation can be either a single
		value (the name of the operation), or an array containing the name of the operation
		along with one or more values. Here is the representation of all the possible
		forms of a single criteria:

		{{{
		$aCriteria = array(
			'field_1' => 'operation',
			'field_2' => array('operation'),
			'field_3' => array('operation', 'value'),
			'field_4' => array('operation', 'value', 'more values', ...),
		);
		}}}

		All the criteria are logically connected with the operator AND.
		In our example, that means field_1 AND field_2 AND field_3 AND field_4.
		You can however also use following construct to logically connect criteria:

		{{{
		$aCriteria = array('logical operator', $aCriteriaA, $aCriteriaB)
		}}}

		Most of the time though you won't need to use this directly.
		The subset* methods are doing the job for you.

		@param $aCriteria The criteria to filter with.
	*/

	public function __construct($aSubsetCriteria = array())
	{
		$this->aSubsetCriteria = $aSubsetCriteria;
	}

	/**
		Build various JOINs defined using the $aRefSets property.
		To change the type of the JOIN, change the value of the $sJoinType property.

		@param $aMeta The metadata for the table associated with this set.
		@return The JOINs built according to the $aRefSets property.
		@throw InvalidArgumentException No set was given or the set's table doesn't have a primary key.
	*/

	protected function buildJoin($aMeta)
	{
		$oDb = $this->getDb();
		$sJoin = '';

		foreach ($this->aRefSets as $aRef) {
			if (!is_array($aRef))
				$aRef = array('set' => $aRef);

			empty($aRef['set']) and burn('InvalidArgumentException', _WT('No set was given.'));

			$oRefSet = new $aRef['set'];
			$oRefSet->setDb($oDb);
			$aRefMeta = $oRefSet->getMeta();

			empty($aRefMeta['primary']) and burn('InvalidArgumentException',
				sprintf(_WT('The reference table %s do not have a primary key.'), $aRefMeta['table']));

			$sJoin .= ' ' . $this->sJoinType . ' ' . $aRefMeta['table'] . ' ON (TRUE';

			if (empty($aRef['key'])) {
				// Use the linked set's primary key columns names

				foreach ($aRefMeta['primary'] as $sColumn) {
					$sColumn = $oDb->escapeIdent($sColumn);
					$sJoin .= ' AND ' . $aMeta['table'] . '.' . $sColumn . '=' . $aRefMeta['table'] . '.' . $sColumn;
				}
			} else {
				// Use the given column names association

				foreach ($aRef['key'] as $sColumn => $sRefColumn)
					$sJoin .= ' AND ' . $aMeta['table'] . '.' . $oDb->escapeIdent($sColumn)
						. '=' . $aRefMeta['table'] . '.' . $oDb->escapeIdent($sRefColumn);
			}

			$sJoin .= ')';
		}

		return $sJoin;
	}

	/**
		Build the WHERE clause for the subset's criteria.

		@see weeDbSetScaffold::__construct
		@return string The WHERE clause built according to the criteria.
		@throw InvalidArgumentException A criteria operation or logical operation is invalid.
	*/

	protected function buildWhere()
	{
		if (empty($this->aSubsetCriteria))
			return ' WHERE TRUE';

		if (is_int(key($this->aSubsetCriteria)))
			return ' WHERE ' . $this->buildWhereLogical($this->getDb(), $this->aSubsetCriteria);
		return ' WHERE ' . $this->buildWhereCriteria($this->getDb(), $this->aSubsetCriteria);
	}

	/**
		Build a criteria part of the WHERE clause.

		@see weeDbSetScaffold::__construct
		@param $oDb The database associated with this set.
		@param $aCriteria The criteria.
		@return string The criteria part of the WHERE clause built according to the criteria.
		@throw InvalidArgumentException The criteria operation is not in the list of valid operations.
	*/

	protected function buildWhereCriteria($oDb, $aCriteria)
	{
		$sWhere = 'TRUE';

		foreach ($aCriteria as $sField => $mOperation) {
			$sWhere .= ' AND ' . $oDb->escapeIdent($sField) . ' ';

			if (is_array($mOperation)) {
				in_array($mOperation[0], $this->aValidCriteriaOperators) or burn('InvalidArgumentException',
					sprintf(_WT('The criteria operation requested, "%s", do not exist.'), $mOperation[0]));

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
					sprintf(_WT('The criteria operation requested, "%s", do not exist.'), $mOperation));

				$sWhere .= $mOperation;
			}
		}

		return $sWhere;
	}

	/**
		Build a logical part of a WHERE clause.

		@see weeDbSetScaffold::__construct
		@param $oDb The database associated with this set.
		@param $aCriteria The logical criteria.
		@return string The logical part of the WHERE clause built according to the criteria.
		@throw InvalidArgumentException The criteria logical operation is not in the list of valid logical operations.
	*/

	protected function buildWhereLogical($oDb, $aCriteria)
	{
		in_array($aCriteria[0], $this->aValidCriteriaLogicalOperators) or burn('InvalidArgumentException',
			sprintf(_WT('The criteria logical operator given, "%s", do not exist.'), $aCriteria[0]));

		$sWhere = '(';

		if (is_int(key($aCriteria[1])))
			$sWhere .= $this->buildWhereLogical($oDb, $aCriteria[1]);
		else
			$sWhere .= $this->buildWhereCriteria($oDb, $aCriteria[1]);

		$sWhere .= ') ' . $aCriteria[0] . ' (';

		if (is_int(key($aCriteria[2])))
			$sWhere .= $this->buildWhereLogical($oDb, $aCriteria[2]);
		else
			$sWhere .= $this->buildWhereCriteria($oDb, $aCriteria[2]);

		return $sWhere . ')';
	}

	/**
		Count the number of rows that would be returned by a fetchAll query.
		A JOIN is performed on the reference tables if any are provided.

		@return integer The number of rows in the table.
	*/

	public function count()
	{
		$aMeta = $this->getMeta();

		// Faster equivalent
		if ($this->sJoinType == 'LEFT OUTER JOIN' && empty($this->aSubsetCriteria))
			return $this->queryValue('SELECT COUNT(*) FROM ' . $aMeta['table']);

		return $this->queryValue('SELECT COUNT(*) FROM ' . $aMeta['table'] . $this->buildJoin($aMeta) . $this->buildWhere());
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

		$sQuery = 'SELECT * FROM ' . $aMeta['table'] . $this->buildJoin($aMeta) . ' WHERE TRUE';
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
		@throw InvalidArgumentException $iCount must be provided if $iOffset is given.
	*/

	public function fetchSubset($iOffset = 0, $iCount = 0)
	{
		is_int($iOffset) or burn('InvalidArgumentException', _WT('$iOffset must be an integer.'));
		is_int($iCount) or burn('InvalidArgumentException', _WT('$iCount must be an integer.'));
		(empty($iCount) && !empty($iOffset)) and burn('InvalidArgumentException',
			_WT('$iCount must be provided when $iOffset is given.'));

		$aMeta = $this->getMeta();

		$sQuery = 'SELECT * FROM ' . $aMeta['table'] . $this->buildJoin($aMeta) . $this->buildWhere();
		if (!empty($this->sOrderBy))
			$sQuery .= ' ORDER BY ' . $this->sOrderBy;
		if (!empty($iCount))
			$sQuery .= ' LIMIT ' . $iCount . ' OFFSET ' . $iOffset;

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
		(null === $mPrimaryKey || (is_array($mPrimaryKey) && empty($mPrimaryKey)))
			and burn('InvalidArgumentException', _WT('The primary key value must not be empty.'));

		if (!is_array($mPrimaryKey))
			$mPrimaryKey = array($aMeta['primary'][0] => $mPrimaryKey);

		$aDiff = array_diff($aMeta['primary'], array_keys($mPrimaryKey));
		empty($aDiff) or burn('InvalidArgumentException', _WT('The primary key value is incomplete.'));

		return $mPrimaryKey;
	}

	/**
		Get the criteria defining the subset.

		@see weeDbSetScaffold::__construct
		@return array The criteria used to define the subset.
	*/

	public function getSubsetCriteria()
	{
		return $this->aSubsetCriteria;
	}

	/**
		Get the metadata for the table associated with the set.

		The metadata returned contains information about:
		* table:	The full table name, properly quoted.
		* columns:	An array of all the columns names.
		* primary:	An array of all the primary key columns names.

		@return array The metadata for the table associated with this set.
	*/

	public function getMeta()
	{
		if (empty($this->aMeta)) {
			$oTable = $this->getDb()->meta()->table($this->sTableName);
			$this->aMeta = array(
				'table'		=> $oTable->quotedName(),
				'colsobj'	=> $oTable->columns(),
				'columns'	=> $oTable->columnsNames(),
				'primary'	=> $oTable->primaryKey()->columnsNames(),
			);
		}

		return $this->aMeta;
	}

	/**
		Return the reference sets associated with this one.

		@return array The reference sets (the $aRefSets property).
	*/

	public function getRefSets()
	{
		return $this->aRefSets;
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
		Defines the order of the rows returned by the fetchAll and fetchSubset methods.

		The parameter can be either a scalar value (for example, the name of the column to use for sorting)
		or an associative array. When providing an array, you can give as many sort options as you want,
		using the key as the field name and the value as the sort operation requested, which can be either
		ASC or DESC currently. If only the value is provided without a key, it is used as the field name
		and the order will default to ASC.

		@param $mOrderBy The order in which the rows should be sorted.
		@return $this
		@throw InvalidArgumentException The ORDER BY modifier is not in the list of valid modifiers.
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
					in_array(strtoupper($mValue), $this->aValidOrderByModifiers)
						or burn('InvalidArgumentException', sprintf(_WT('The ORDER BY modifier requested, %s, do not exist.'), $mValue));

					$this->sOrderBy .= ', ' . $oDb->escapeIdent($mName) . ' ' . $mValue;
				}
			}

			$this->sOrderBy = substr($this->sOrderBy, 2);
		}

		return $this;
	}

	/**
		Create a new subset based on a logical operation between two sets.
		The logical operation is: A op B. Set A is the $this object.
		Set B is the set given with the $mSet argument.

		@param $sLogical The logical operation to perform between the two sets.
		@param $mSet Set B of the operation. This can be either a criteria array or a weeDbSetScaffold object.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	protected function subset($sLogical, $mSet)
	{
		if (is_object($mSet))
			$mSet = $mSet->getSubsetCriteria();

		$sClass = get_class($this);
		$o = new $sClass(array(
			$sLogical,
			$this->aSubsetCriteria,
			$mSet,
		));
		$o->setDb($this->getDb());
		return $o;
	}

	/**
		Returns a subset representing the complement of $mSet in $this. In other words: $this - $mSet.

		@param $mSet The set to obtain the complement of.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	public function subsetComplementOf($mSet)
	{
		return $this->subset('AND NOT', $mSet);
	}

	/**
		Returns a subset representing the intersection of $mSet and $this.

		@param $mSet The set to intersect with.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	public function subsetIntersect($mSet)
	{
		return $this->subset('AND', $mSet);
	}

	/**
		Returns a subset representing the symmetric difference of $mSet and $this.

		@param $mSet The set to do the symmetric difference with.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	public function subsetSymDiff($mSet)
	{
		return $this->subset('XOR', $mSet);
	}

	/**
		Returns a subset representing the union of $mSet and $this.

		@param $mSet The set to unite with.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	public function subsetUnion($mSet)
	{
		return $this->subset('OR', $mSet);
	}
}
