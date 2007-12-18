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
	Container for a web document opened by weeWebBrowser.
*/

class weeWebDocument extends weeSimpleXMLHack
{
	/**
		Search for a pattern using strpos.

		@param	$sPattern	The string to find in the document.
		@return	bool		True if pattern is found in the document, false otherwise.
	*/

	public function find($sPattern)
	{
		return (false !== strpos($this->asXML(), $sPattern));
	}

	/**
		Search for a pattern using preg_match (perl compatible regular expressions).

		@param	$sPattern	The pattern to find in the document.
		@param	$aMatches	See http://php.net/preg-match
		@param	$iFlags		See http://php.net/preg-match
		@param	$iOffset	See http://php.net/preg-match
		@return	bool		True if pattern is found in the document, false otherwise.
		@see	http://php.net/preg-match
	*/

	public function regex($sPattern, array &$aMatches = null, $iFlags = 0, $iOffset = 0)
	{
		fire(isset($iFlags) && !ctype_digit($iFlags), 'InvalidArgumentException', '$iFlags must be an integer.');
		fire(isset($iOffset) && !ctype_digit($iOffset), 'InvalidArgumentException', '$iOffset must be an integer');

		return (1 == preg_match($sPattern, $this->asXML(), $aMatches, $iFlags, $iOffset));
	}
}

?>
