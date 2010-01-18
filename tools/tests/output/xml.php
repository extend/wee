<?php

$o = new weeXMLEncoder;

// weeXMLEncoder::encode

$this->isEqual('win', $o->encode('win'),
	_WT('weeXMLEncoder::encode should return its argument untouched if it does not contain any special character.'));

$this->isEqual("Time to say &apos;night.", $o->encode("Time to say 'night."),
	_WT("weeXMLEncoder::encode should encode any single quote character encountered."));

$this->isEqual('&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot; &amp; &quot;è_é&quot;', $o->encode('">_>" & "<_<" & "è_é"'),
	_WT('weeXMLEncoder::encode should return the expected encoded value.'));

$this->isEqual('東方妖々夢', $o->encode('東方妖々夢'),
	_WT('weeXMLEncoder::encode should not encode Unicode characters.'));

// weeXMLEncoder::decode

$this->isEqual('win', $o->decode('win'),
	_WT('weeXMLEncoder::decode should return its argument untouched if it does not contain any XML entity.'));

$this->isEqual('">_>" & "<_<" & "&egrave;_&eacute;"', $o->decode('&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot; &amp; &quot;&egrave;_&eacute;&quot;'),
	_WT('weeXMLEncoder::decode should return the expected decoded value.'));
