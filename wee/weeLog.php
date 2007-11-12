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

function weeLog($sMessage, $sType = 'notice', $sFile = null, $sLine = null)
{
	static $rFile = null;

	if (is_null($rFile))
	{
		//TODO:allow to define the log file in the configuration file (add to weeApplication constructor)
		if (defined('WEE_LOG_FILE'))
			$sLogFile = WEE_LOG_FILE;
		else
			$sLogFile = sys_get_temp_dir() . '/wee.log';

		$rFile = fopen($sLogFile, 'a');
	}

	// Format message: [date] [type] [file:line] message

	$sLog = '[' . @date('r') . '] [' . $sType . '] ';
	if (!empty($sFile))
		$sLog .= '[' . $sFile . ':' . $sLine . '] ';
	$sLog .= str_replace("\n", "\n\t", $sMessage);

	fwrite($rFile, $sLog . "\n");
}

?>
