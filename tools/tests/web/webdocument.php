<?php

$sCookieFile 	= ROOT_PATH . 'tools/tests/web/cookie.txt';
$sUrl 			= 'http://www.google.com/xhtml';
$aMatched		= array();

touch($sCookieFile);
try {
	$oWebBrowser 	= new weeWebBrowser($sCookieFile);
	$oWebDocument 	= $oWebBrowser->fetchDoc($sUrl);
	
	$this->isTrue($oWebDocument->find('google'), 
		sprintf(_('weeWebDocument::find should find the string "google" in the document returned by %s.'), $sUrl));

	$this->isTrue($oWebDocument->regex('/google/'),
		sprintf(_('weeWebDocument::regex should find the pattern "/google/" in the document returned by %s.'), $sUrl));
} catch (InvalidArgumentException $e) {
	$this->fail('weeWebBrowser should not throw an InvalidArgumentException when trying to find the pattern "/google/".');
}

try {
	$oWebBrowser 	= new weeWebBrowser($sCookieFile);
	$oWebDocument 	= $oWebBrowser->fetchDoc($sUrl);

	$this->isFalse($oWebDocument->find('6oogle'), 
		sprintf(_('weeWebDocument::find should not find the string "6oogle" in the document returned by %s.'), $sUrl));

	$this->isFalse($oWebDocument->regex('/6oogle/', $aMatched, '1', '2'),
		sprintf(_('weeWebDocument::regex should not find the pattern "/6oogle/" in the document returned by %s.'), $sUrl));

	$this->fail(_('weeWebBrowser should throw an InvalidArgumentException when trying to find the pattern /6oogle/.'));
} catch (InvalidArgumentException $e) {}

try {
	$oWebBrowser 	= new weeWebBrowser($sCookieFile);
	$oWebDocument 	= $oWebBrowser->fetchDoc('http://www.google.com/search?q=web+extend');
	$this->fail(_('weeWebBrowser should throw an BadXMLException when trying to fetch the document returned by http://www.google.com/search?q=web+extend.'));
} catch (BadXMLException $e) {}
