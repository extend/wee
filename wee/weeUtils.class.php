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

class weeUtils extends Namespace
{
	public static function nl2uli($s)
	{
		if (empty($s))
			return $s;

		if (substr($s, strlen($s) - 2) == "\r\n")
			$s = substr($s, 0, strlen($s) - 2);

		$s = str_replace("\r\n", '</li><li>', $s);
		return '<ul><li>' . $s . '</li></ul>';
	}

	public static function getPathInfo($bRemoveQueryString = true)
	{
		//TODO:maybe not that a good idea to overwrite this variable
		while (substr($_SERVER['REQUEST_URI'], 0, 2) == '/.')
			$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 2);

		$sPathInfo = urldecode(substr($_SERVER['REQUEST_URI'], 1 + strlen($_SERVER['SCRIPT_NAME'])));

		if ($bRemoveQueryString && !empty($_SERVER['QUERY_STRING']) && substr($sPathInfo, -strlen($_SERVER['QUERY_STRING'])) == $_SERVER['QUERY_STRING'])
			$sPathInfo = substr($sPathInfo, 0, -1 - strlen($_SERVER['QUERY_STRING']));

		return $sPathInfo;
	}
}

?>
