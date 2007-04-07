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
	Base class for action controllers.
*/

abstract class weeActionController
{
	/**
		Do the specified action.

		All the additional arguments are passed to the corresponding doAction* method.

		@param	$sAction	Action being done.
		@return	string		The result of the action.
	*/

	public function doAction($sAction)
	{
		fire(empty($sAction) || !ctype_print($sAction));

		$sMethod = 'doAction' . $sAction;
		fire(!method_exists($this, $sMethod));

		$aArgs = func_get_args();
		unset($aArgs[0]);

		$s = 'return $this->' . $sMethod . '(';
		foreach ($aArgs as $i => $mArg)
			$s .= '$aArgs[' . $i . '],';
		$s = substr($s, 0, -1) . ');';

		return eval($s);
	}
}

?>
