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

/*
	// weeDatabaseRow

	§(eq, 'id', 1)

	§::load('mysql');
	$sSQL = §()->select('test.label')->where(§(eq, 'id', 1)->and(not, §(in, 'year', '2000', '2001', '2005')))->limit(1);
	$sSQL->where()->and(§(ne, 'label', 'oeuf'));

	"SELECT label FROM test WHERE 'id'='1' AND 'year' NOT IN ('2000', '2001', '2005') LIMIT 1"
*/

class weeDatabaseQuery
{
	public static	$criteriaClass;
	public static	$queryClass;

	protected		$sAction;
	protected		$oCriteria;
	protected		$sTable;
	protected		$aValues = array();

	public function build($oDatabase)
	{
		$sSQL = $this->sAction . ' `' . $this->sTable . '` SET ';

		foreach ($this->aValues as $sName => $sValue)
		{
			if (empty($sValue) || $sValue{0} != '`')
				$sValue	= $oDatabase->escape($sValue);
			$sSQL .= $sName . '=' . $sValue . ',';
		}

		$sSQL = substr($sSQL, 0, -1);

		if ($this->sAction == 'update')
			$sSQL .= ' WHERE ' . $this->oCriteria->build($oDatabase);

		return $sSQL;
	}

	public function insert($sTable)
	{
		$this->sAction	= 'insert';
		$this->sTable	= $sTable;
		return $this;
	}

	public function set($sName, $sValue)
	{
		$this->aValues[$sName] = $sValue;
		return $this;
	}

	public function update($sTable)
	{
		$this->sAction	= 'update';
		$this->sTable	= $sTable;
		return $this;
	}

	public function where($oWhereCriteria = null)
	{
		fire(empty(self::$criteriaClass), 'IllegalStateException');
		fire(!is_object($oWhereCriteria), 'UnexpectedValueException');

		if (is_null($oWhereCriteria))
		{
			if (empty($this->oCriteria))
				$this->oCriteria = new self::$criteriaClass;
			return $this->oCriteria;
		}
		else
		{
			fire(!empty($this->oCriteria), 'IllegalStateException');
			$this->oCriteria = $oWhereCriteria;
			return $this;
		}
	}
}

function §()
{
	if (func_num_args() == 0)
		return new weeDatabaseQuery::$queryClass;
	else
	{
		$s = 'return new weeDatabaseQuery::$criteriaClass(';
		foreach (func_get_args() as $mArg)
			$s .= "'" . $mArg . "',";
		$s = substr($s, 0, -1) . ');';

		return eval($s);
	}
}

?>
