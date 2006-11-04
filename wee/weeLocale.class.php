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

final class weeLocale extends Namespace
{
	public static function set($sLang, $sEncoding, $sLocalePath = './', $sTextDomain = 'messages')
	{
		//TODO:check values

		putenv('LANG=' . $sLang);
		setlocale(LC_MESSAGES, $sLang . '.' . strtoupper($sEncoding));
		bindtextdomain($sTextDomain, $sLocalePath);
		bind_textdomain_codeset($sTextDomain, $sEncoding);
		textdomain($sTextDomain);
	}
}

?>
