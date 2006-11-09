<?php

/*
	Dev:Extend Web Library
	Copyright (c) 2006 Dev:Extend

	This software is licensed under the Dev:Extend Public License. You can use,
	copy, modify and/or distribute the software under the terms of this License.

	This software is distributed WITHOUT ANY WARRANTIES, including, but not
	limited to, the implied warranties of MERCHANTABILITY and FITNESS FOR A
	PARTICULAR PURPOSE. See the Dev:Extend Public License for more details.

	You should have received a copy of the Dev:Extend Public License along with
	this software; if not, you can download it at the following url:
	http://dev-extend.eu/license/.
*/

if (!defined('ALLOW_INCLUSION')) die;

abstract class weeFeed
{
	protected $aFeed	= array();
	protected $aEntries	= array();

	public function __call($sName, $aArgs)
	{
		//TODO: can $sName be null?
		fire(sizeof($aArgs) != 1);

		$aFeed[$sName] = $aArgs[0];
	}

	abstract public function __toString();

	public function entries($aEntries)
	{
		foreach ($aEntries as $aEntry)
			$this->entry($aEntry);
	}

	public function entry($aEntry)
	{
		$this->aEntries[] = $aEntry;
	}
}

?>
