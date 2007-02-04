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
	Experimental namespace for locale handling.
*/

final class weeLocale extends Namespace
{
	/**
		Obtains the current locale.

		@return string The current locale. @see http://php.net/setlocale for more information.
	*/

	public static function getCurrent()
	{
		return setlocale(LC_ALL, 0);
	}

	/**
		Changes locale.

		@param $sLang		Language of the locale.
		@param $sEncoding	Encoding of the locale file.
		@param $sLocalePath	Path to the locale directory.
		@param $sTextDomain	Name of the domain in which locales are stored. Usually the name of the .mo file.
	*/

	public static function set($sLang, $sEncoding, $sLocalePath = './', $sTextDomain = 'messages')
	{
		fire(!function_exists('gettext'), 'ConfigurationException');

		//TODO:check values

		putenv('LANG=' . $sLang);
		setlocale(LC_MESSAGES, $sLang . '.' . strtoupper($sEncoding));
		bindtextdomain($sTextDomain, $sLocalePath);
		bind_textdomain_codeset($sTextDomain, $sEncoding);
		textdomain($sTextDomain);
	}
}

?>
