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

if (!defined('TPL_PATH'))	define('TPL_PATH',	ROOT_PATH . 'tpl/');
if (!defined('TPL_EXT'))	define('TPL_EXT',	'.tpl');

class weeTemplate
{
	protected $sFilename;
	protected $aData;

	public function __construct($sTemplate, array $aData = array())
	{
		$this->sFilename	= TPL_PATH . $sTemplate . TPL_EXT;
		fire(!file_exists($this->sFilename), 'FileNotFoundException');

		$this->aData		= $aData;
	}

	public function set($mName, $mValue)
	{
		$this->aData[$mName] = $mValue;
		return $this;
	}

	public function __toString()
	{
		extract(weeOutput::encodeArray($this->aData));

		ob_start();
		require($this->sFilename);
		$s = ob_get_contents();
		ob_end_clean();

		return $s;
	}
}

?>
