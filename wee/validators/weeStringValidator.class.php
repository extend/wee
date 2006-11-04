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

class weeStringValidator implements weeValidator
{
	protected $aArgs;
	protected $sError;
	protected $bHasError	= false;

	protected $aErrorList	= array(
		'len'	=> 'Input must have exactly %len% characters',
		'max'	=> 'Input must have at most %max% characters',
		'min'	=> 'Input must have at least %min% characters');

	public function __construct($mValue, $aArgs)
	{
		$this->aArgs = $aArgs;

		if (isset($aArgs['len']) && strlen($mValue) != $aArgs['len'])
			$this->setError('len');
		elseif (isset($aArgs['max']) && strlen($mValue) > $aArgs['max'])
			$this->setError('max');
		elseif (isset($aArgs['min']) && strlen($mValue) < $aArgs['min'])
			$this->setError('min');
	}

	public function getError()
	{
		return $this->sError;
	}

	public function hasError()
	{
		return $this->bHasError;
	}

	protected function setError($sType)
	{
		$this->bHasError	= true;

		$sMsg = $sType . '_error';
		if (!empty($this->aArgs[$sMsg]))	$this->sError = $this->aArgs[$sMsg];
		else								$this->sError = $this->aErrorList[$sType];

		$this->sError		= str_replace('%' . $sType . '%', $this->aArgs[$sType], _($this->sError));
	}

	public static function test($mValue, $aArgs)
	{
		$o = new self($mValue, $aArgs);
		return $o->hasError();
	}
}

?>
