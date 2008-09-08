<?php

$sCookieFile		= ROOT_PATH . 'tools/tests/web/cookie.txt';
$sXmlNotValidFile	= 'http://www.w3schools.com/XML/note_error.xml';
$sXmlValidFile		= 'http://www.w3schools.com/XML/note.xml';

touch($sCookieFile);
chmod($sCookieFile, 0000);
try {
	$oWebBrowser = new weeWebBrowser($sCookieFile);
	$this->fail(sprintf(_('weeWebBrowser should throw a NotPermittedException when trying to access the file %s.'), $sCookieFile));
} catch (NotPermittedException $e) {}
chmod($sCookieFile, 0644);

try {
	$oWebBrowser = new weeWebBrowser($sCookieFile);
} catch (NotPermittedException $e) {
	$this->fail(sprintf(_('weeWebBrowser should not throw a NotPermittedException when trying to access the file %s.'), $sCookieFile));
}

try {
	$oWebBrowser = new weeWebBrowser($sCookieFile);
	$oWebBrowser->fetchDoc($sXmlValidFile);
} catch (BadXMLException $e) {
	$this->fail(sprintf(_('weeWebBrowser should not throw a BadXMLException when fetching the file %s.'), $sXmlValidFile));
}

try {
	$oWebBrowser = new weeWebBrowser($sCookieFile);
	$oWebBrowser->fetchDoc($sXmlNotValidFile);
	$this->fail(sprintf(_('weeWebBrowser should throw a BadXMLException when fetching the file %s.'), $sXmlNotValidFile));
} catch (BadXMLException $e) {}
