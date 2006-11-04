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

class weeMySQLResult extends weeDatabaseResult
{
	private $rResult;

	public function __construct($rResult)
	{
		fire(!is_resource($rResult), 'InvalidArgumentException');
		$this->rResult = $rResult;
	}

	public function __destruct()
	{
		mysql_free_result($this->rResult);
	}

	public function fetch()
	{
		$a = mysql_fetch_assoc($this->rResult);
		fire($a === false, 'DatabaseException');

		if ($this->bEncodeResults)
			return weeOutput::encodeArray($a);
		return $a;
	}

	public function fetchAll()
	{
		$aAll = array();
		foreach ($this as $a)
			$aAll[] = $a;
		return $aAll;
	}

	public function numResults()
	{
		$i = mysql_num_rows($this->rResult);
		fire($i === false, 'DatabaseException');

		return $i;
	}

	// SPL: Iterator

	private $aCurrentFetch;
	private $iCurrentIndex;

	public function current()
	{
		if ($this->bEncodeResults)	return weeOutput::encodeArray($this->aCurrentFetch);
		else						return $this->aCurrentFetch;
	}

	public function key()
	{
		return $this->iCurrentIndex;
	}

	public function next()
	{
		$this->iCurrentIndex++;
	}

	public function rewind()
	{
		$this->iCurrentIndex = 0;
		@mysql_data_seek($this->rResult, 0);
	}

	public function valid()
	{
		$this->aCurrentFetch = mysql_fetch_assoc($this->rResult);
		return ($this->aCurrentFetch !== false);
	}
}

?>
