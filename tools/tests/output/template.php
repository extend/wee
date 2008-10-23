<?php

weeXHTMLOutput::select();

class weeTemplate_test extends weeTemplate {
	public function __construct() {}

	public function mkLink($sLink, $aArgs = array()) {
		return parent::mkLink($sLink, $aArgs);
	}
}

$o = new weeTemplate_test;

$this->isEqual('/foo&amp;/bar?&lt;=blah&amp;answer=42', $o->mkLink('/foo&/bar', array('<' => 'blah', 'answer' => 42)),
	_('weeTemplate::mkLink should encode the link with the weeOutput::encodeValue method.'));

$this->isEqual('/foo/bar?a=1&amp;b=2', $o->mkLink('/foo/bar?a=1', array('b' => '2')),
	_('weeTemplate::mkLink should append the given parameters if the base link already contain a query string.'));

$this->isEqual('/foo/bar?space=a+b', $o->mkLink('/foo/bar', array('space' => 'a b')),
	_('weeTemplate::mkLink should encode any URL parameter with the urlencode function.'));

$this->isEqual('/foo/bar?entity=%26', $o->mkLink('/foo/bar', array('entity' => '&')),
	_('weeTemplate::mkLink should decode the values of the URL parameters with the weeOutput::decode method before encoding them with the urlencode function.'));
