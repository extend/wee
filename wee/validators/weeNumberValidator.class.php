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

class weeNumberValidator implements weeValidator
{
	protected $aArgs;
	protected $sError;
	protected $bHasError	= false;

	protected $aErrorList	= array(
		'max'	=> 'Input must not be greater than %max%',
		'min'	=> 'Input must not be smaller than %min%',
		'nan'	=> 'Input must be a number',
		'float'	=> 'Input must be a decimal value',
		'int'	=> 'Input must be an integer value');

	public function __construct($mValue, $aArgs)
	{
		$this->aArgs = $aArgs;

		if (!is_numeric($mValue))
			$this->setError('nan');
		elseif (isset($aArgs['max']) && $mValue > $aArgs['max'])
			$this->setError('max');
		elseif (isset($aArgs['min']) && $mValue < $aArgs['min'])
			$this->setError('min');
		else
		{
			if (empty($aArgs['format']))
				$aArgs['format'] = 'int';

			if (substr($mValue, 0, 1) == '-')
				$mValue = substr($mValue, 1);

			if ($aArgs['format'] == 'float' && !ctype_digit(str_replace('.', '', $mValue)))
				setError('float');
			elseif ($aArgs['format'] == 'int' && !ctype_digit($mValue))
				setError('int');
		}
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

		$this->sError	= str_replace('%' . $sType . '%', $this->aArgs[$sType], _($this->sError));
	}

	public static function test($mValue, $aArgs)
	{
		$o = new self($mValue, $aArgs);
		return $o->hasError();
	}
}

?>
