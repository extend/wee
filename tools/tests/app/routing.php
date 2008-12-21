<?php

class weeApplication_testTranslateRoute extends weeApplication
{
	protected $aConfig = array(
		'route.is' => 'aboutus',
		'route.~(\w+)/(\w+)' => '$2?user=$1',
		'route.~(\w+)/(\w+)/(\w+)' => '$2/$3?user=$1',
		'route.(\d{4})/(\d{2})/(\d{2})/(.*)' => 'viewpost?year=$1&month=$2&day=$3&uid=$4',
	);

	// We need a public constructor.
	public function __construct() {}

	// For later tests
	public function setStrictRouting()
	{
		$this->aConfig['routing.strict'] = 1;
	}

	// We are testing this method, expose it.
	public function translateRoute($sPathInfo, &$aGet)
	{
		return parent::translateRoute($sPathInfo, $aGet);
	}
}

$o = new weeApplication_testTranslateRoute;

$sPathInfo = 'is';
$aGet = array();
$this->isEqual('aboutus', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = 'isnot';
$aGet = array();
$this->isEqual($sPathInfo, $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = 'itis';
$aGet = array();
$this->isEqual($sPathInfo, $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '~essen/blog';
$aGet = array();
$this->isEqual('blog', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
$this->isEqual(array('user' => 'essen'), $aGet,
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '~essen/blog42';
$aGet = array('user' => 'nox');
$this->isEqual('blog42', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
$this->isEqual(array('user' => 'essen'), $aGet,
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '~{42}/blog';
$aGet = array();
$this->isEqual($sPathInfo, $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '~essen/{42}';
$aGet = array();
$this->isEqual($sPathInfo, $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '~essen/blog/rss';
$aGet = array();
$this->isEqual('blog/rss', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
$this->isEqual(array('user' => 'essen'), $aGet,
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '2008/11/06/rowo-part-2-simple-is-easy';
$aGet = array();
$this->isEqual('viewpost', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
$this->isEqual(array('year' => '2008', 'month' => '11', 'day' => '06', 'uid' => 'rowo-part-2-simple-is-easy'), $aGet,
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '2008/11/06/rowo-part-2-simple-is-easy/';
$aGet = array();
$this->isEqual('viewpost', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
$this->isEqual(array('year' => '2008', 'month' => '11', 'day' => '06', 'uid' => 'rowo-part-2-simple-is-easy/'), $aGet,
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

$sPathInfo = '2008/11/06/rowo-part-2-simple-is-easy/yes-it-is';
$aGet = array();
$this->isEqual('viewpost', $o->translateRoute($sPathInfo, $aGet),
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
$this->isEqual(array('year' => '2008', 'month' => '11', 'day' => '06', 'uid' => 'rowo-part-2-simple-is-easy/yes-it-is'), $aGet,
	sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

// Now test strict routing

$o->setStrictRouting();

try {
	$sPathInfo = '~essen/blog/rss';
	$aGet = array();
	$this->isEqual('blog/rss', $o->translateRoute($sPathInfo, $aGet),
		sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));
	$this->isEqual(array('user' => 'essen'), $aGet,
		sprintf(_WT('The translation of the route "%s" failed.'), $sPathInfo));

	$sPathInfo = '~essen/';
	$aGet = array();
	$o->translateRoute($sPathInfo, $aGet);
	$this->fail(sprintf(_WT('The translation of the route "%s" should have failed.'), $sPathInfo));
} catch (RouteNotFoundException $e) {
}
