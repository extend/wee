<?php

$sCookieFile		= ROOT_PATH . 'app/tmp/cookie.txt';
$sXmlNotWellFormed	= 'http://www.w3schools.com/XML/note_error.xml';
$sXmlWellFormed		= 'http://www.w3schools.com/XML/note.xml';

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
	$oWebBrowser->fetchDoc($sXmlWellFormed);
} catch (BadXMLException $e) {
	$this->fail(sprintf(_('weeWebBrowser should not throw a BadXMLException when fetching the file %s.'), $sXmlWellFormed));
}

try {
	$oWebBrowser = new weeWebBrowser($sCookieFile);
	$oWebBrowser->fetchDoc($sXmlNotWellFormed);
	$this->fail(sprintf(_('weeWebBrowser should throw a BadXMLException when fetching the file %s.'), $sXmlNotWellFormed));
} catch (BadXMLException $e) {}

