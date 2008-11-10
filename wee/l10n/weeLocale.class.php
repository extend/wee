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

if (!defined('ALLOW_INCLUSION')) die;

/**
	Extends intl Locale class and adds a constructor useful when used
	as a driver of the application module, allowing the auto-selection
	of a locale based on the HTTP_ACCEPT_LANGUAGE header.
*/

class weeLocale extends Locale
{
	/**
		Initialize the locale.

		The parameters array can contain:
		- default:	The default locale to use when others aren't available.
		- auto:		Whether to automatically try to select the best locale based on the HTTP_ACCEPT_LANGUAGE header.

		@param $aParams The parameters listed above.
	*/

	public function __construct($aParams)
	{
		if (!empty($aParams['default']))
			locale_set_default($aParams['default'])
				or burn('InvalidArgumentException', 'Setting the default locale failed.');

		if (!empty($aParams['auto']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			try {
				$this->set(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']));
			} catch (UnexpectedValueException $e) {
				//TODO:maybe try the default locale here and otherwise go back to 'C'?
			}
		}
	}

	/**
		Return the locale used currently by the application.

		@return The current locale.
	*/

	public function get()
	{
		return setlocale(LC_ALL, 0);
	}

	/**
		Change the locale used by the application.

		@param $sLocale The new locale.
	*/

	public function set($sLocale)
	{
		// We need the complete locale name
		if (strpos($sLocale, '_') === false)
			$sLocale .= '_' . strtoupper($sLocale);

		setlocale(LC_ALL, $sLocale . '.UTF-8')
			or burn('UnexpectedValueException', 'An error occurred while trying to set the locale.');
	}
}
