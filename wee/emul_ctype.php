<?php

/**
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
	@see http://php.net/ctype_alnum
*/

function ctype_alnum($s)
{
	return (bool)preg_match('/^[[:alnum:]]+$/', $s);
}

/**
	@see http://php.net/ctype_alpha
*/

function ctype_alpha($s)
{
	return (bool)preg_match('/^[[:alpha:]]+$/', $s);
}

/**
	@see http://php.net/ctype_cntrl
*/

function ctype_cntrl($s)
{
	return (bool)preg_match('/^[[:cntrl:]]+$/', $s);
}

/**
	@see http://php.net/ctype_digit
*/

function ctype_digit($s)
{
	return (bool)preg_match('/^[[:digit:]]+$/', $s);
}

/**
	@see http://php.net/ctype_graph
*/

function ctype_graph($s)
{
	return (bool)preg_match('/^[[:graph:]]+$/', $s);
}

/**
	@see http://php.net/ctype_lower
*/

function ctype_lower($s)
{
	return (bool)preg_match('/^[[:lower:]]+$/', $s);
}

/**
	@see http://php.net/ctype_print
*/

function ctype_print($s)
{
	return (bool)preg_match('/^[[:print:]]+$/', $s);
}

/**
	@see http://php.net/ctype_punct
*/

function ctype_punct($s)
{
	return (bool)preg_match('/^[[:punct:]]+$/', $s);
}

/**
	@see http://php.net/ctype_space
*/

function ctype_space($s)
{
	return (bool)preg_match('/^[[:space:]]+$/', $s);
}

/**
	@see http://php.net/ctype_upper
*/

function ctype_upper($s)
{
	return (bool)preg_match('/^[[:upper:]]+$/', $s);
}

/**
	@see http://php.net/ctype_xdigit
*/

function ctype_xdigit($s)
{
	return (bool)preg_match('/^[[:xdigit:]]+$/', $s);
}

?>
