<?php

/*
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

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
	Application module that complements the intl Locale class
	by adding mechanisms to select automatically the locale
	depending on the request sent by the browser.
*/

class weeLocale
{
	/**
		Map between the 2-letter language codes and the locale names.

		Only defines a few of the most popular languages by default,
		and only defines one locale per language. If you need a finer control
		over the languages you will be required to extend this class. Take
		care not to use an existing code (in the ISO standard) when doing so.

		@see http://en.wikipedia.org/wiki/Global_Internet_usage
		@see http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
	*/

	protected $aLocaleMap = array(
		'ar' => 'ar_SA', // Arabic
		'ca' => 'ca_ES', // Catalan
		'cs' => 'cs_CZ', // Czech
		'da' => 'da_DK', // Danish
		'de' => 'de_DE', // German
		'el' => 'el_GR', // Greek
		'en' => 'en_US', // English
		'es' => 'es_ES', // Spanish
		'fa' => 'fa_IR', // Persian
		'fi' => 'fi_FI', // Finnish
		'fr' => 'fr_FR', // French
		'he' => 'iw_IL', // Hebrew
		'hi' => 'hi_IN', // Hindi
		'hu' => 'hu_HU', // Hungarian
		'is' => 'is_IS', // Icelandic
		'it' => 'it_IT', // Italian
		'ja' => 'ja_JP', // Japanese
		'ko' => 'ko_KR', // Korean
		'ms' => 'ms_MY', // Malay
		'nl' => 'nl_NL', // Dutch
		'no' => 'no_NO', // Norwegian
		'pl' => 'pl_PL', // Polish
		'pt' => 'pt_BR', // Portuguese
		'ro' => 'ro_RO', // Romanian
		'ru' => 'ru_RU', // Russian
		'sh' => 'sh_YU', // Serbo-Croatian
		'sk' => 'sk_SK', // Slovak
		'sl' => 'sl_SI', // Slovenian
		'sv' => 'sv_SE', // Swedish
		'th' => 'th_TH', // Thai
		'tr' => 'tr_TR', // Turkish
		'uk' => 'uk_UA', // Ukrainian
		'vi' => 'vi_VN', // Vietnamese
		'zh' => 'zh_CN', // Chinese
	);

	/**
		Initialize the locale.

		The parameters array can contain:
		- auto:		Whether to automatically try to select the best locale based on the HTTP_ACCEPT_LANGUAGE header.
		- default:	The default locale to use when others aren't available.

		@param $aParams The parameters listed above.
	*/

	public function __construct($aParams = array())
	{
		function_exists('locale_set_default') or burn('ConfigurationException',
			_WT('The intl PHP extension is required by the weeLocale application driver.'));

		if (!empty($aParams['default']))
			locale_set_default($aParams['default'])
				or burn('InvalidArgumentException', _WT('Setting the default locale failed.'));

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

	public function set($sLocale, $sCodeSet = 'UTF-8', $sModifier = null)
	{
		if (strlen($sLocale) == 2)
			$sLocale = array_value($this->aLocaleMap, $sLocale, $sLocale);

		if (strlen($sLocale) > 1) {
			if (!is_null($sCodeSet))
				$sLocale .= '.' . $sCodeSet;
			if (!is_null($sModifier))
				$sLocale .= '@' . $sModifier;
		}

		setlocale(LC_ALL, $sLocale)
			or burn('UnexpectedValueException', _WT('An error occurred while trying to set the locale.'));
	}

	/**
		Change the locale used by the application by using the pathinfo
		to determine which language is requested.

		The language MUST exist in the $aLocaleMap property to be detected.

		@param $sPathInfo The pathinfo for this request.
		@return string The pathinfo minus the language part, if any.
	*/

	public function setFromPathInfo($sPathInfo)
	{
		$aSplit = explode('/', $sPathInfo, 2);

		if (array_key_exists($aSplit[0], $this->aLocaleMap)) {
			$this->set($aSplit[0]);
			$sPathInfo = substr($sPathInfo, 3);
		}

		return $sPathInfo;
	}
}
