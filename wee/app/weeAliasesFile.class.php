<?php

/**
	Web:Extend
	Copyright (c) 2007 Dev:Extend

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

/**
	Aliases file wrapper.
*/

class weeAliasesFile extends weeFileConfig
{
	/**
		Create an aliases file object.

		@param $sFilename Path and filename to the aliases file.
	*/

	public function __construct($sFilename)
	{
		parent::__construct($sFilename);
		uksort($this->aConfig, array($this, 'uksortCallback'));
	}

	/**
		Search for an alias in a path info and resolve it.

		@return	string	The path info without aliases.
	*/

	public function resolveAlias($sPathInfo)
	{
		foreach ($this->aConfig as $sAlias => $sLink)
		{
			$i = strlen($sAlias);

			if (!isset($sPathInfo[$i - 1]))
				continue;

			if (isset($sPathInfo[$i]) && $sPathInfo[$i] != '/')
				continue;

			if ($sAlias == substr($sPathInfo, 0, $i))
				return $sLink . substr($sPathInfo, $i);
		}

		return $sPathInfo;
	}

	/**
		Callback for the uksort() function.

		The aliases array is sorted by the number of parts of each of them
		in decreasing order as we need to check aliases from the longest to the
		shortest.

		@see	http://php.net/uksort
	*/

	protected function uksortCallback($sLeft, $sRight)
	{
		return substr_count($sRight, '/') - substr_count($sLeft, '/');
	}
}
