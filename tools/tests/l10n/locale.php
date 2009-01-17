<?php

if (!function_exists('locale_set_default'))
	return $this->skip();

class weeLocale_test extends weeLocale
{
	public static $sLocale;

	// We need to overload it to retrieve the result
	public function set($sLocale, $sEncoding = 'UTF-8', $sModifier = null)
	{
		self::$sLocale = $sLocale;
	}
}

// 'auto' parameter

weeLocale_test::$sLocale = null;
$sBefore = array_value($_SERVER, 'HTTP_ACCEPT_LANGUAGE');
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en;q=0.8,fr;q=0.9';
$o = new weeLocale_test(array('auto' => true));
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $sBefore;

$this->isEqual('fr', weeLocale_test::$sLocale,
	_WT('The locale was not correctly detected using the HTTP_ACCEPT_LANGUAGE header.'));

// ::get

$o = new weeLocale_test;

$sBefore = setlocale(LC_ALL, 'en_US');
setlocale(LC_ALL, 'C');
$this->isEqual('C', $o->get(),
	_WT('The locale was no correctly retrieved by weeLocale::get.'));
setlocale(LC_ALL, $sBefore);
