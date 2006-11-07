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

class weeConfirmValidator implements weeFormValidator
{
	protected $aArgs;
	protected $aData;
	protected $sError;
	protected $bHasError	= false;
	protected $mValue;

	protected $aErrorList	= array(
		'invalid'	=> 'Input confirmation failed');

	public function __construct($mValue, array $aArgs = array())
	{
		$this->aArgs	= $aArgs;
		$this->mValue	= $mValue;
	}

	public function getError()
	{
		return $this->sError;
	}

	public function hasError()
	{
		fire(empty($this->aData), 'InvalidStateException');

		if (is_array($this->mValue))
			;//TODO:compare arrays
		elseif ($this->aData[(string)$this->aArgs['with']] != $this->mValue)
			$this->setError('invalid');

		return $this->bHasError;
	}

	public function setData($aData)
	{
		$this->aData = $aData;
	}

	protected function setError($sType)
	{
		$this->bHasError	= true;

		$sMsg = $sType . '_error';
		if (!empty($this->aArgs[$sMsg]))	$this->sError = $this->aArgs[$sMsg];
		else								$this->sError = $this->aErrorList[$sType];

		$this->sError		= _($this->sError);
	}

	public function setWidget($oWidget)
	{
	}

	public static function test($mValue, array $aArgs = array())
	{
		$o = new self($mValue, $aArgs);
		return $o->hasError();
	}
}

?>
