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

define('lt',	'lt');
define('le',	'le');
define('eq',	'eq');
define('ne',	'ne');
define('ge',	'ge');
define('gt',	'gt');
define('in',	'in');
define('not',	'not');

abstract class weeDatabaseCriteria
{
	protected $aCriteria = array();

	public function __construct($iOperator)
	{
		$a					= func_get_args();
		$a['op']			= null;
		$this->aCriteria[]	= $a;
	}

	public function __call($sOp, $aArgs)
	{
		$aArgs['op']		= $sOp;
		$this->aCriteria[]	= $aArgs;
		return $this;
	}

	abstract public function build($oDatabase);
}

?>
