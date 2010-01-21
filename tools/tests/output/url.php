<?php // encoding: utf-8

function test_url_to_string($sURL = null, $aData = array())
{
	$o = new weeURL($sURL, $aData);
	return $o->toString();
}

function test_encoded_url_to_string($sURL = null, $aData = array())
{
	$o = new weeURL($sURL);
	$o->setEncoder(new weeXHTMLEncoder);
	$o->addData($aData);
	return $o->toString();
}

// no set*, no encoder

$this->isNull(test_url_to_string(),
	_WT('weeURL::toString should return null if the base URL is empty and there is no data.'));

$this->isEqual('/foo', test_url_to_string('/foo'),
	_WT('weeURL::toString should return the link as-is if there is no data.'));

$this->isEqual('/fée', test_url_to_string('/fée'),
	_WT('weeURL::toString should not encode unicode characters.'));

$this->isEqual('/foo&/bar?<=blah&answer=42', test_url_to_string('/foo&/bar', array('<' => 'blah', 'answer' => 42)),
	_WT('Without encoder, weeURL::toString cannot encode the resulting link.'));

try {
	test_url_to_string('/foo/bar?a=1', array('b' => '2'));
	$this->fail(_WT('A weeURL object should never be created when the base URL already contains parameters.'));
} catch (InvalidArgumentException $e) {
}

$this->isEqual('/foo/bar?space=a+b', test_url_to_string('/foo/bar', array('space' => 'a b')),
	_WT('weeURL::toString should encode any URL parameter with the urlencode function.'));

$this->isEqual('/foo/bar?entity=%26amp%3B', test_url_to_string('/foo/bar', array('entity' => '&amp;')),
	_WT('Without encoder, weeURL::toString cannot decode the values of the URL parameters; they are only urlencoded.'));

$this->isEqual('/foo#bar', test_url_to_string('/foo#bar'),
	_WT('weeURL::toString should return the link as-is if no new parameter are to be added to the query string, even with a hash.'));

// addData, no encoder

$o = new weeURL('/foo/bar', array('arg' => 'value'));
$this->isEqual('/foo/bar?fish=spam&arg=value', $o->addData(array('fish' => 'spam'))->toString(),
	_WT('Without encoder, weeURL::toString should include the arguments added through weeTemplate::addData.'));

$o = new weeURL('/foo/bar', array('arg' => 'value'));
$this->isEqual('/foo/bar?fish=spam%26amp%3Bspam&arg=value', $o->addData(array('fish' => 'spam&amp;spam'))->toString(),
	_WT('Without encoder, weeURL::toString should include the arguments added through weeTemplate::addData without trying to decode them.'));

$o = new weeURL('/foo/bar', array('arg' => 'value'));
$this->isEqual('/foo/bar?arg=another+value', $o->addData(array('arg' => 'another value'))->toString(),
	_WT('Without encoder, weeURL::toString should override the arguments added through the constructor.'));

$o = new weeURL('/foo/bar#tender', array('arg' => 'value'));
$this->isEqual('/foo/bar?arg=another+value#tender', $o->addData(array('arg' => 'another value'))->toString(),
	_WT('WWithout encoder, weeURL::toString should override the arguments added through the constructor even with a hash.'));

// no set*, with encoder

$this->isNull(test_encoded_url_to_string(),
	_WT('weeURL::toString should return null if the base URL is empty and there is no data.'));

$this->isEqual('/foo', test_encoded_url_to_string('/foo'),
	_WT('weeURL::toString should return the link as-is if there is no data.'));

$this->isEqual('/fée', test_encoded_url_to_string('/fée'),
	_WT('weeURL::toString should not encode unicode characters.'));

$this->isEqual('/foo&amp;/bar?&lt;=blah&amp;answer=42', test_encoded_url_to_string('/foo&/bar', array('<' => 'blah', 'answer' => 42)),
	_WT('When an encoder is present, weeURL::toString should encode the link with the weeEncoder::encode method.'));

try {
	test_encoded_url_to_string('/foo/bar?a=1', array('b' => '2'));
	$this->fail(_WT('A weeURL object should never be created when the base URL already contains parameters.'));
} catch (InvalidArgumentException $e) {
}

$this->isEqual('/foo/bar?space=a+b', test_encoded_url_to_string('/foo/bar', array('space' => 'a b')),
	_WT('weeURL::toString should encode any URL parameter with the urlencode function.'));

$this->isEqual('/foo/bar?entity=%26', test_encoded_url_to_string('/foo/bar', array('entity' => '&amp;')),
	_WT('When an encoder is present, weeURL::toString should decode the values of the URL parameters with the weeEncoder::decode method before encoding them with the urlencode function.'));

$this->isEqual('/foo#bar', test_encoded_url_to_string('/foo#bar'),
	_WT('weeURL::toString should return the link as-is if no new parameter are to be added to the query string, even with a hash.'));

// addData, with encoder

$o = new weeURL('/foo/bar', array('arg' => 'value'));
$o->setEncoder(new weeXHTMLEncoder);
$this->isEqual('/foo/bar?fish=spam&amp;arg=value', $o->addData(array('fish' => 'spam'))->toString(),
	_WT('When an encoder is present, weeURL::toString should include the arguments added through weeTemplate::addData.'));

$o = new weeURL('/foo/bar', array('arg' => 'value'));
$o->setEncoder(new weeXHTMLEncoder);
$this->isEqual('/foo/bar?fish=spam%26spam&amp;arg=value', $o->addData(array('fish' => 'spam&amp;spam'))->toString(),
	_WT('When an encoder is present, weeURL::toString should include the arguments added through weeTemplate::addData by decoding them first.'));

$o = new weeURL('/foo/bar', array('arg' => 'value'));
$o->setEncoder(new weeXHTMLEncoder);
$this->isEqual('/foo/bar?arg=another+value', $o->addData(array('arg' => 'another value'))->toString(),
	_WT('When an encoder is present, weeURL::toString should override the arguments added through the constructor.'));

$o = new weeURL('/foo/bar#tender', array('arg' => 'value'));
$o->setEncoder(new weeXHTMLEncoder);
$this->isEqual('/foo/bar?arg=another+value#tender', $o->addData(array('arg' => 'another value'))->toString(),
	_WT('When an encoder is present, weeURL::toString should override the arguments added through the constructor even with a hash.'));
