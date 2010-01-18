<?php // encoding: utf-8

$o = new weeXHTMLEncoder;

// weeXHTMLEncoder::encode

$this->isEqual('win', $o->encode('win'),
	_WT('weeXHTMLEncoder::encode should return its argument untouched if it does not contain any special character.'));

$this->isEqual("Time to say 'night.", $o->encode("Time to say 'night."),
	_WT("weeXHTMLEncoder::encode should return any single quote character untouched."));

$this->isEqual('&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot; &amp; &quot;è_é&quot;', $o->encode('">_>" & "<_<" & "è_é"'),
	_WT('weeXHTMLEncoder::encode should return the expected encoded value.'));

$this->isEqual('東方妖々夢', $o->encode('東方妖々夢'),
	_WT('weeXHTMLEncoder::encode should not encode Unicode characters.'));

// weeXHTMLEncoder::decode

$this->isEqual('win', $o->decode('win'),
	_WT('weeXHTMLEncoder::decode should return its argument untouched if it does not contain any XHTML entity.'));

$this->isEqual('">_>" & "<_<" & "&egrave;_&eacute;"', $o->decode('&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot; &amp; &quot;&egrave;_&eacute;&quot;'),
	_WT('weeXHTMLEncoder::decode should return the expected decoded value.'));
