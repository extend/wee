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

abstract class weeDatabase
{
	abstract public function __construct($aParams = array());
	abstract public function __destruct();

	private function __clone()
	{
	}

	protected function buildSafeQuery($aArguments)
	{
		$aParts		= explode('?', $aArguments[0]);

		$iNbParts	= sizeof($aParts);
		$iNbArgs	= sizeof($aArguments);

		fire($iNbParts != $iNbArgs && $iNbParts - 1 != $iNbArgs, 'UnexpectedValueException');

		$q = null;
		for ($i = 1; $i < sizeof($aArguments); $i++)
			$q .= $aParts[$i - 1] . $this->Escape($aArguments[$i]);

		if ($iNbParts == $iNbArgs)
			$q .= $aParts[$iNbParts - 1];

		return $q;
	}

	abstract public function escape($mValue);
	abstract public function getLastError();
	abstract public function getPKId($sName = null);
	abstract public function numAffectedRows();
	abstract public function numQueries();
	abstract public function query($mQueryString);
}

?>
