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

/**
	Returns the array value if exists, else a default value.
	Simpler form than using the conditional operators, and returns null by default, which we usually want.

	@param	$aArray		The array.
	@param	$sKey		The key to look for in the array.
	@param	$mIfNotSet	The default value.
	@return	mixed
	@todo	Find a better place for this function.
*/

function array_value($aArray, $sKey, $mIfNotSet = null)
{
	if (isset($aArray[$sKey]))
		return $aArray[$sKey];
	return $mIfNotSet;
}

?>
