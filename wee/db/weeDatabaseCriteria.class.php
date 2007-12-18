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

/**
	Base class for criteria handling.
	Should work with all SQL-compliant databases.
*/

class weeDatabaseCriteria
{
	/**
		Contains all the criteria given with their corresponding logical operators.
	*/

	protected $aCriteria	= array();

	/**
		PHP code for comparisons allowed by the criteria.
	*/

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

	/**
		Initialize the object with a criteria.
	*/

	public function __construct($iOperator)
	{
		$a					= func_get_args();
		$a['op']			= null;
		$this->aCriteria[]	= $a;
	}

	/**
		Add a criteria with its corresponding logical operator.
		
		@overload and($sOp, $aArgs)	Add a criteria connected to the previous with AND.
		@overload or($sOp, $aArgs)	Add a criteria connected to the previous with OR.
		@param	$sOp	The logical operator (AND, OR). This is not really a parameter, but the name of the function used.
		@param	$aArgs	The criteria itself.
		@return	$this
	*/

	public function __call($sOp, $aArgs)
	{
		$aArgs['op']		= $sOp;
		$this->aCriteria[]	= $aArgs;
		return $this;
	}

	/**
		Build the SQL criteria.
		You shouldn't have to call it yourself, weeDatabaseQuery will do it for you.

		@param	$oDatabase	The database object for this query.
		@return	string		The SQL criteria built.
	*/

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
				fire(empty($a), 'InvalidArgumentException', "Missing parameter for 'in' criteria.");

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

	/**
		Replace the tags in the PHP code by their respective values, escaping if needed.

		@param	$oDatabase	The database object for this query.
		@param	$sSearch	The tag to replace.
		@param	$mReplace	The value to replace with.
		@param	$sSubject	The PHP code where the replacement will be done.
		@return	string		The PHP code after the replacement.
	*/

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
