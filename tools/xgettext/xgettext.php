<?php

/*
	Web:Extend xgettext tool
	Copyright (c) 2006-2010 Dev:Extend

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

$aOptions = getopt('af:');
$aOptions === false and die("getopt failed to get the options from the command line.\n");

if (!isset($aOptions['f'])) {
	echo "usage: php xgettext.php [-a] -f path\n";
	return -1;
}

if (!is_array($aOptions['f']))
	$aOptions['f'] = array($aOptions['f']);

if (isset($aOptions['a']))
	$sFunc = '_T';
else
	$sFunc = '_WT';

function xgettext_file($sFilename, $sFunc)
{
	$aMatches = array();
	preg_match_all('/(?<=' . $sFunc . "\\(('|\")).+?(?=\\1)/", file_get_contents($sFilename), $aMatches, PREG_PATTERN_ORDER);
	return $aMatches[0];
}

$aText = array();
foreach ($aOptions['f'] as $sPath) {
	if (!is_dir($sPath))
		$aText = array_merge($aText, xgettext_file($sPath, $sFunc));
	else {
		$aFiles = array();
		$oIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sPath));
		foreach ($oIterator as $sFilename => $oFile) {
			if ($oFile->isDir())
				continue;

			$sExt = substr(strrchr($sFilename, '.'), 1);
			if ($sExt != 'php' && $sExt != 'tpl')
				continue;

			$aFiles[] = $sFilename;
		}

		foreach ($aFiles as $sFilename)
			$aText = array_merge($aText, xgettext_file($sFilename, $sFunc));
	}
}

$aText = array_unique($aText);
foreach ($aText as $s) {
	$s = str_replace('"', '\"', $s);
	echo 'msgid "' . $s . "\"\nmsgstr \"" . $s . "\"\n\n";
}
