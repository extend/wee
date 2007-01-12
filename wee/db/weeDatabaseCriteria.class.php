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

class weeDatabaseCriteria
{
	protected $aCriteria	= array();
	protected $aComparisons	= array(
		lt	=> '$1 < $2',
		le	=> '$1 <= $2',
		eq	=> '$1 = $2',
		ne	=> '$1 != $2',
		ge	=> '$1 >= $2',
		gt	=> '$1 > $2',
		in	=> '$1 IN ($2)',
		not	=> 'NOT $1',
	);

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

	public function build($oDatabase)
	{
		$sSQL = null;

		foreach ($this->aCriteria as $a)
		{
			if (!empty($a['op']))
				$sSQL .= ' ' . $a['op'];
			$sSQL .= ' (';

			if ($a[0] == in)
			{
				$s = $this->replace($oDatabase, '$1', $a[1], $this->aComparisons[in]);

				unset($a[0], $a[1], $a['op']);
				fire(empty($a), 'InvalidArgumentException');

				$sIn = null;
				foreach ($a as $mValue)
				{
					if (is_array($mValue))
						foreach ($mValue as $m)
							$sIn .= $oDatabase->escape($m) . ',';
					else	$sIn .= $oDatabase->escape($mValue) . ',';
				}

				$sSQL .= str_replace('$2', substr($sIn, 0, strlen($sIn) - 1), $s);
			}
			elseif ($a[0] == not)
				$sSQL .= $this->replace($oDatabase, '$1', $a[1], $this->aComparisons[not]);
			else
			{
				$s = $this->replace($oDatabase, '$1', $a[1], $this->aComparisons[$a[0]]);
				$s = $this->replace($oDatabase, '$2', $a[2], $s);

				$sSQL .= $s;
			}

			$sSQL .= ')';
		}

		return $sSQL;
	}

	protected function replace($oDatabase, $sSearch, $mReplace, $sSubject)
	{
		if (is_object($mReplace))
			return str_replace($sSearch, $mReplace->build($oDatabase), $sSubject);

//TODO:if the following line fails, this usually means that the id of the updated row wasn't provided with the form data

		if ($mReplace[0] == '`')
			$mReplace = substr($mReplace, 1, -1);
		else
			$mReplace = $oDatabase->escape($mReplace);

		return str_replace($sSearch, $mReplace, $sSubject);
	}
}

?>
