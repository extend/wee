<?php

/*
	Web:Extend
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

// Currently only handles 16x16 png icons

$aOptions = getopt('f:n:');
$aOptions === false and die("getopt failed to get the options from the command line.\n");

if (!isset($aOptions['f']) || !isset($aOptions['n'])) {
	echo "usage: php cssicons.php -f icons_path -n css_name\n";
	return -1;
}

define('ALLOW_INCLUSION', 1);
define('ROOT_PATH', '../../');
require(ROOT_PATH . 'wee/wee.php');

$aFiles = array_merge(
	glob($aOptions['f'] . '/actions/*.png'),
	glob($aOptions['f'] . '/apps/*.png'),
	glob($aOptions['f'] . '/categories/*.png'),
	glob($aOptions['f'] . '/devices/*.png'),
	glob($aOptions['f'] . '/emblems/*.png'),
	glob($aOptions['f'] . '/emotes/*.png'),
	glob($aOptions['f'] . '/mimetypes/*.png'),
	glob($aOptions['f'] . '/places/*.png'),
	glob($aOptions['f'] . '/status/*.png')
);

$sCSS = '.icon{background:url(' . $aOptions['n'] . '.png) no-repeat;display:inline-block;height:16px;line-height:16px;overflow:hidden;position:relative;top:2px;width:16px}';
$sHTML = '<html><head><title>Icons test page: ' . $aOptions['n'] . '</title><link rel="stylesheet" type="text/css"  href="' . $aOptions['n'] . '.css"/></head><body>';

$rBigFile = imagecreatetruecolor(16, 16 * count($aFiles));
imagealphablending($rBigFile, false);
imagesavealpha($rBigFile, true);

$i = 0;
foreach ($aFiles as $sFilename) {
	$r = imagecreatefrompng($sFilename);
	imagecopy($rBigFile, $r, 0, $i, 0, 0, 16, 16);
	imagedestroy($r);

	$sClass = substr(strrchr($sFilename, '/'), 1, -4);
	$sCSS .= '.' . $sClass . '{background-position:0px ' . (-$i) . 'px}';
	$sHTML .= '<span class="icon ' . $sClass . '">&nbsp;</span> ' . $sClass . '<br/>';

	$i += 16;
}

file_put_contents($aOptions['n'] . '.html', $sHTML . '</body></html>');
file_put_contents($aOptions['n'] . '.css', $sCSS);

// We could compress more, but it doesn't decrease the size significantly
imagepng($rBigFile, $aOptions['n'] . '.png', 1);
imagedestroy($rBigFile);
