<?php

class weeXHTMLOutput_test extends weeXHTMLOutput {
	public function __construct() {}
}

$o = new weeXHTMLOutput_test;

// weeXHTMLOutput::encode

$this->isEqual('win', $o->encode('win'),
	_('weeXHTMLOutput::encode should return its argument untouched if it does not contain any special character.'));

$this->isEqual("Time to say 'night.", $o->encode("Time to say 'night."),
	_("weeXHTMLOutput::encode should return any single quote character untouched."));

$this->isEqual('&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot; &amp; &quot;&egrave;_&eacute;&quot;', $o->encode('">_>" & "<_<" & "è_é"'),
	_('weeXHTMLOutput::encode should return the expected encoded value.'));

$this->isEqual('東方妖々夢', $o->encode('東方妖々夢'),
	_('weeXHTMLOutput::encode should not encode Unicode characters.'));

// weeXHTMLOutput::decode

$this->isEqual('win', $o->decode('win'),
	_('weeXHTMLOutput::decode should return its argument untouched if it does not contain any XHTML entity.'));

$this->isEqual('">_>" & "<_<" & "è_é"', $o->decode('&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot; &amp; &quot;&egrave;_&eacute;&quot;'),
	_('weeXHTMLOutput::decode should return the expected decoded value.'));
