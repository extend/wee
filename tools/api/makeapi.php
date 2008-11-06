<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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

if ($argc != 2)
{
	echo "usage: php makeapi.php output_path\n";
	return -1;
}

define('NO_CACHE', 1);
define('ALLOW_INCLUSION', 1);
require('wee/wee.php');

$o = new weeDocumentorXML;
file_put_contents(
	$argv[1] . 'api.xml',
	$o	->docClassFromPath('wee')
		->docClass('Printable')
		->docFunc('fire')
		->docFunc('burn')
		->docFunc('_T')
		->docFunc('_WT')
		->docFunc('array_value')
		->docFunc('rmdir_recursive')
		->docFunc('xmlspecialchars')
		->toString()
);

echo $argv[1] . "api.xml created successfully.\n";
