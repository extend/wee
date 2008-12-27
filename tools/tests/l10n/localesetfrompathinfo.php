<?php

class weeLocale_testSetFromPathInfo extends weeLocale
{
	public static $sLocale;

	// We need to overload it to retrieve the result
	public function set($sLocale, $sEncoding = 'UTF-8', $sModifier = null)
	{
		self::$sLocale = $sLocale;
	}
}

$o = new weeLocale_testSetFromPathInfo;

weeLocale_testSetFromPathInfo::$sLocale = null;
$sTestPathInfo = 'fr/aboutus';
$sPathInfo = $o->setFromPathInfo($sTestPathInfo);
$this->isEqual('fr', weeLocale_testSetFromPathInfo::$sLocale,
	sprintf(_WT('The locale for "%s" could not be retrieved.'), $sTestPathInfo));
$this->isEqual('aboutus', $sPathInfo,
	sprintf(_WT('The resulting pathinfo for "%s" is incorrect.'), $sTestPathInfo));

weeLocale_testSetFromPathInfo::$sLocale = null;
$sTestPathInfo = 'zh';
$sPathInfo = $o->setFromPathInfo($sTestPathInfo);
$this->isEqual('zh', weeLocale_testSetFromPathInfo::$sLocale,
	sprintf(_WT('The locale for "%s" could not be retrieved.'), $sTestPathInfo));
$this->isEqual('', $sPathInfo,
	sprintf(_WT('The resulting pathinfo for "%s" is incorrect.'), $sTestPathInfo));

weeLocale_testSetFromPathInfo::$sLocale = null;
$sTestPathInfo = 'ja/';
$sPathInfo = $o->setFromPathInfo($sTestPathInfo);
$this->isEqual('ja', weeLocale_testSetFromPathInfo::$sLocale,
	sprintf(_WT('The locale for "%s" could not be retrieved.'), $sTestPathInfo));
$this->isEqual('', $sPathInfo,
	sprintf(_WT('The resulting pathinfo for "%s" is incorrect.'), $sTestPathInfo));

weeLocale_testSetFromPathInfo::$sLocale = null;
$sTestPathInfo = 'test/fr/aboutus';
$sPathInfo = $o->setFromPathInfo($sTestPathInfo);
$this->isTrue(empty(weeLocale_testSetFromPathInfo::$sLocale),
	sprintf(_WT('No locale should have been retrieved for "%s".'), $sTestPathInfo));
$this->isEqual($sTestPathInfo, $sPathInfo,
	sprintf(_WT('The resulting pathinfo for "%s" is incorrect.'), $sTestPathInfo));

weeLocale_testSetFromPathInfo::$sLocale = null;
$sTestPathInfo = 'cz/aboutus';
$sPathInfo = $o->setFromPathInfo($sTestPathInfo);
$this->isTrue(empty(weeLocale_testSetFromPathInfo::$sLocale),
	sprintf(_WT('No locale should have been retrieved for "%s".'), $sTestPathInfo));
$this->isEqual($sTestPathInfo, $sPathInfo,
	sprintf(_WT('The resulting pathinfo for "%s" is incorrect.'), $sTestPathInfo));
