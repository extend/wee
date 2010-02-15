<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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

	This class currently supports PostgreSQL, MySQL and SQLite.

	To use it, simply extend it and define the $sModel property to the name of the weeDbModelScaffold class,
	and the $sTableName to the name of the table in the database represented by this set.

	Some operations done by this class may have a higher cost than using a normal weeDbSet and
	writing SQL queries directly. This class is mainly a scaffold for 90% of the common operations
	done with a database. It's highly useful to quickly start a project, and can be used without
	any problem on most applications. If you need high performance for some operations you might
	consider writing those queries manually and using the scaffolding for all other operations.
*/

abstract class weeDbSetScaffold extends weeDbSet implements Countable
{
	/**
		Defines the type of JOIN to do when joining reference tables.
	*/

	protected $sJoinType = 'LEFT OUTER JOIN';

	/**
		Internal list of ambiguous keys. Those usually are foreign keys with
		the same name in both linked tables and can't be used directly without
		the table identifier prepended. We store this identifier here when we
		detect the problem when building joins, and reuse it on other parts of
		the generated query.
	*/

	protected $aJoinAmbiguousKeys = array();

	/**
		The metadata for the table associated with this set.

		The metadata contains information about:
		* table:	The full table name, properly quoted.
		* columns:	An array of all the columns names.
		* primary:	An array of all the primary key columns names.
	*/

	protected $aMeta;

	/**
		The ORDER BY part of the SELECT queries.
		Can be defined directly here or by using the orderBy method.
	*/

	protected $mOrderBy;

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
		'NOT',		// logical negation of A
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
				sprintf(_WT('The reference table %s does not have a primary key.'), $aRefMeta['table']));

			$sJoin .= ' ' . $this->sJoinType . ' ' . $aRefMeta['table'] . ' ON (';
			$sAnd = '';

			if (empty($aRef['key'])) {
				// Use the linked set's primary key columns names

				foreach ($aRefMeta['primary'] as $sColumn) {
					$this->aJoinAmbiguousKeys[$sColumn] = $aMeta['table'];

					$sColumn = $oDb->escapeIdent($sColumn);
					$sJoin .= $sAnd . $aMeta['table'] . '.' . $sColumn . '=' . $aRefMeta['table'] . '.' . $sColumn;
					$sAnd = ' AND ';
				}
			} else {
				// Use the given column names association

				foreach ($aRef['key'] as $sColumn => $sRefColumn) {
					$sJoin .= $sAnd . $aMeta['table'] . '.' . $oDb->escapeIdent($sColumn)
						. '=' . $aRefMeta['table'] . '.' . $oDb->escapeIdent($sRefColumn);
					$sAnd = ' AND ';
				}
			}

			$sJoin .= ')';
		}

		return $sJoin;
	}

	/**
		Build the contents of the ORDER BY clause to be used to sort queries returned by fetchSubset/fetchAll.

		@return string The contents of the ORDER BY clause.
		@throw InvalidArgumentException The ORDER BY modifier is not in the list of valid modifiers.
	*/

	public function buildOrderBy()
	{
		$oDb = $this->getDb();

		if (!is_array($this->mOrderBy))
			return $oDb->escapeIdent($this->mOrderBy);

		$sOrderBy = '';

		foreach ($this->mOrderBy as $mName => $mValue) {
			$bNoValue = is_int($mName);

			if ($bNoValue)
				$mName = $mValue;

			$sOrderBy .= ', ';
			if (isset($this->aJoinAmbiguousKeys[$mName]))
				$sOrderBy .= $this->aJoinAmbiguousKeys[$mName] . '.';
			$sOrderBy .= $oDb->escapeIdent($mName);

			if (!$bNoValue) {
				in_array(strtoupper($mValue), $this->aValidOrderByModifiers)
					or burn('InvalidArgumentException', sprintf(_WT('The ORDER BY modifier requested, %s, does not exist.'), $mValue));

				$sOrderBy .= ' ' . $mValue;
			}
		}

		return substr($sOrderBy, 2);
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
			return ''; // equivalent to WHERE TRUE

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
		$sWhere = ''; // equivalent to TRUE
		$sAnd = '';

		foreach ($aCriteria as $sField => $mOperation) {
			$sWhere .= $sAnd;
			$sAnd = ' AND ';

			if (isset($this->aJoinAmbiguousKeys[$sField]))
				$sWhere .= $this->aJoinAmbiguousKeys[$sField] . '.';
			$sWhere .= $oDb->escapeIdent($sField) . ' ';

			if (is_array($mOperation)) {
				in_array($mOperation[0], $this->aValidCriteriaOperators) or burn('InvalidArgumentException',
					sprintf(_WT('The criteria operation requested, "%s", does not exist.'), $mOperation[0]));

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
					sprintf(_WT('The criteria operation requested, "%s", does not exist.'), $mOperation));

				$sWhere .= $mOperation;
			}
		}

		return $sWhere;
	}

	/**
		Build a logical part of a WHERE clause.

		All the logical operators expect 2 arguments except NOT, which explains the special cases for it.

		@see weeDbSetScaffold::__construct
		@param $oDb The database associated with this set.
		@param $aCriteria The logical criteria.
		@return string The logical part of the WHERE clause built according to the criteria.
		@throw InvalidArgumentException The criteria logical operation is not in the list of valid logical operations.
	*/

	protected function buildWhereLogical($oDb, $aCriteria)
	{
		in_array($aCriteria[0], $this->aValidCriteriaLogicalOperators) or burn('InvalidArgumentException',
			sprintf(_WT('The criteria logical operator given, "%s", does not exist.'), $aCriteria[0]));

		if ($aCriteria[0] == 'NOT')
			$sWhere = 'NOT (';
		else
			$sWhere = '(';

		if (is_int(key($aCriteria[1])))
			$sWhere .= $this->buildWhereLogical($oDb, $aCriteria[1]);
		else
			$sWhere .= $this->buildWhereCriteria($oDb, $aCriteria[1]);

		if ($aCriteria[0] != 'NOT') {
			$sWhere .= ') ' . $aCriteria[0] . ' (';

			if (is_int(key($aCriteria[2])))
				$sWhere .= $this->buildWhereLogical($oDb, $aCriteria[2]);
			else
				$sWhere .= $this->buildWhereCriteria($oDb, $aCriteria[2]);
		}

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

		$sQuery = 'DELETE FROM ' . $aMeta['table'] . ' WHERE';
		$sAnd = '';

		foreach ($aMeta['primary'] as $sField) {
			$sQuery .= $sAnd;
			$sAnd = ' AND ';

			if (isset($this->aJoinAmbiguousKeys[$sField]))
				$sQuery .= $this->aJoinAmbiguousKeys[$sField] . '.';
			$sQuery .= $oDb->escapeIdent($sField) . '=' . $oDb->escape($mPrimaryKey[$sField]) . ' ';
		}

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

		$sQuery = 'SELECT * FROM ' . $aMeta['table'] . $this->buildJoin($aMeta) . ' WHERE';
		$sAnd = '';

		foreach ($aMeta['primary'] as $sField) {
			$sQuery .= $sAnd;
			$sAnd = ' AND ';

			if (isset($this->aJoinAmbiguousKeys[$sField]))
				$sQuery .= $this->aJoinAmbiguousKeys[$sField] . '.';
			$sQuery .= $oDb->escapeIdent($sField) . '=' . $oDb->escape($mPrimaryKey[$sField]) . ' ';
		}

		return $this->queryRow($sQuery . ' LIMIT 1');
	}

	/**
		Alias of fetchSubset with default values. Fetch all rows in the table.

		@return mixed An instance of weeDatabaseResult.
	*/

	public function fetchAll()
	{
		return $this->fetchSubset();
	}

	/**
		Alias of fetchSubset(0, 1)->fetch(). Fetch the first result only.

		@return mixed An instance of weeDatabaseResult.
		@throw DatabaseException The result set does not contain exactly one row.
	*/

	public function fetchOne()
	{
		return $this->fetchSubset(0, 1)->fetch();
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
		if (!empty($this->mOrderBy))
			$sQuery .= ' ORDER BY ' . $this->buildOrderBy();
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

		The model returned does not contain any other value that could be assigned to the
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

		Note that creating subsets do not carry the order set previously, as it is not possible to know
		in advance which set should be used as a base. This can be carried over manually though.

		@param $mOrderBy The order in which the rows should be sorted.
		@return $this
	*/

	public function orderBy($mOrderBy = array())
	{
		$this->mOrderBy = $mOrderBy;
		return $this;
	}

	/**
		Convenience function to create subsets. Create a new set with the given criteria.

		@param $aCriteria The criteria to filter with.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	protected function subset($aCriteria)
	{
		$sClass = get_class($this);
		$o = new $sClass($aCriteria);
		return $o->setDb($this->getDb());
	}

	/**
		Returns a subset representing the complement of $mSet in $this. In other words: $this - $mSet.

		If the criteria for $mSet is empty, we throw a DomainException. Technically the expression
		will always evaluate to 'FALSE' in that case, which isn't wrong per se, but there isn't
		much point performing queries when we can know easily that there will be no results returned.

		If the criteria for $this is empty, the logical expression becomes 'TRUE AND NOT x', or
		in other words 'NOT x'. We return a set evaluating to 'NOT x' directly.

		@param $mSet The set to obtain the complement of.
		@return weeDbSetScaffold The subset created as a result of the operation.
		@throw DomainException The result is the empty set.
	*/

	public function subsetComplementOf($mSet)
	{
		if (is_object($mSet))
			$mSet = $mSet->getSubsetCriteria();

		empty($mSet) and burn('DomainException',
			_WT('The complement is the empty set. It cannot match any row in the table.'));

		if (empty($this->aSubsetCriteria))
			return $this->subset(array('NOT', $mSet));

		return $this->subset(array('AND NOT', $this->aSubsetCriteria, $mSet));
	}

	/**
		Returns a subset representing the intersection of $mSet and $this.

		If either of the sets criteria is empty, we simply create a new set with the other criteria,
		because the logical expression 'TRUE AND x' evaluates to 'x'.

		@param $mSet The set to intersect with.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	public function subsetIntersect($mSet)
	{
		if (is_object($mSet))
			$mSet = $mSet->getSubsetCriteria();

		$aCriteria = null;

		if (empty($mSet))
			$aCriteria = $this->aSubsetCriteria;
		elseif (empty($this->aSubsetCriteria))
			$aCriteria = $mSet;

		if ($aCriteria !== null)
			return $this->subset($aCriteria);

		return $this->subset(array('AND', $this->aSubsetCriteria, $mSet));
	}

	/**
		Returns a subset representing the symmetric difference of $mSet and $this.

		If both sets are empty, we throw an DomainException. Technically the expression
		will always evaluate to 'FALSE' in that case, which isn't wrong per se, but there isn't
		much point performing queries when we can know easily that there will be no results returned.

		If either of the sets criteria is empty, the logical expression becomes 'TRUE XOR x',
		which can be transformed into 'NOT x'. We return a set evaluating to 'NOT x' directly.

		@param $mSet The set to do the symmetric difference with.
		@return weeDbSetScaffold The subset created as a result of the operation.
		@throw DomainException The result is the empty set.
	*/

	public function subsetSymDiff($mSet)
	{
		if (is_object($mSet))
			$mSet = $mSet->getSubsetCriteria();

		empty($mSet) && empty($this->aSubsetCriteria) and burn('DomainException',
			_WT('The symmetric difference is the empty set. It cannot match any row in the table.'));

		$aCriteria = null;

		if (empty($mSet))
			$aCriteria = $this->aSubsetCriteria;
		elseif (empty($this->aSubsetCriteria))
			$aCriteria = $mSet;

		if ($aCriteria !== null)
			return $this->subset(array('NOT', $aCriteria));

		return $this->subset(array('XOR', $this->aSubsetCriteria, $mSet));
	}

	/**
		Returns a subset representing the union of $mSet and $this.

		If either of the sets criteria is empty, the logical expression becomes 'TRUE OR x',
		which always evaluates to 'TRUE'. We return a set evaluating to 'TRUE' directly.

		@param $mSet The set to unite with.
		@return weeDbSetScaffold The subset created as a result of the operation.
	*/

	public function subsetUnion($mSet)
	{
		if (is_object($mSet))
			$mSet = $mSet->getSubsetCriteria();

		if (empty($mSet) || empty($this->aSubsetCriteria))
			return $this->subset(array());

		return $this->subset(array('OR', $this->aSubsetCriteria, $mSet));
	}
}
