<?php // encoding: utf-8

class weeTemplate_test extends weeTemplate {
	public $aData = array();

	public function __construct() {}

	public function mkLink($sLink, $aArgs = array()) {
		return parent::mkLink($sLink, $aArgs);
	}
}

$o = new weeTemplate_test;
$o->setMIMEType('text/html');

// weeTemplate::mkLink

$this->isEqual('/foo', $o->mkLink('/foo'),
	_WT('weeTemplate::mkLink should return the link as-is if no new parameter are to be added to the query string.'));

$this->isEqual('/fée', $o->mkLink('/fée'),
	_WT('weeTemplate::mkLink should return the link encoded with no new content if no new parameter are to be added to the query string and it contains special characters.'));

$this->isEqual('/foo&amp;/bar?&lt;=blah&amp;answer=42', $o->mkLink('/foo&/bar', array('<' => 'blah', 'answer' => 42)),
	_WT('weeTemplate::mkLink should encode the link with the weeEncoder::encode method.'));

$this->isEqual('/foo/bar?b=2&amp;a=1', $o->mkLink('/foo/bar?a=1', array('b' => '2')),
	_WT('weeTemplate::mkLink should add the given parameters to the base link even if it already contains a query string.'));

$this->isEqual('/foo/bar?a=2', $o->mkLink('/foo/bar?a=1', array('a' => '2')),
	_WT('weeTemplate::mkLink should overwrite the base link parameters if a parameter of the same name is given.'));

$this->isEqual('/foo/bar?space=a+b', $o->mkLink('/foo/bar', array('space' => 'a b')),
	_WT('weeTemplate::mkLink should encode any URL parameter with the urlencode function.'));

$this->isEqual('/foo/bar?entity=%26', $o->mkLink('/foo/bar', array('entity' => '&')),
	_WT('weeTemplate::mkLink should decode the values of the URL parameters with the weeEncoder::decode method before encoding them with the urlencode function.'));

$this->isEqual('/foo#bar', $o->mkLink('/foo#bar'),
	_WT('weeTemplate::mkLink should return the link as-is if no new parameter are to be added to the query string, even with a hash.'));

$this->isEqual('/foo/bar?b=2&amp;a=1#tender', $o->mkLink('/foo/bar?a=1#tender', array('b' => '2')),
	_WT('weeTemplate::mkLink should add the given parameters to the base link even if it already contains a query string and a hash.'));

// weeTemplate::addLinkArgs

$o->addLinkArgs(array('arg' => 'value'));

$this->isEqual('/foo/bar?fish=spam&amp;arg=value', $o->mkLink('/foo/bar', array('fish' => 'spam')),
	_WT('weeTemplate::mkLink should include the arguments added through weeTemplate::addLinkArgs.'));

$this->isEqual('/foo/bar?arg=another+value', $o->mkLink('/foo/bar', array('arg' => 'another value')),
	_WT('weeTemplate::mkLink should override the arguments added through weeTemplate::addLinkArgs.'));

$this->isEqual('/foo/bar?arg=another+value#tender', $o->mkLink('/foo/bar#tender', array('arg' => 'another value')),
	_WT('weeTemplate::mkLink should override the arguments added through weeTemplate::addLinkArgs even with a hash.'));

// weeTemplate::set

$this->isEqual(array(), $o->aData,
	_WT('weeTemplate::aData should be empty before any weeTemplate::offsetSet or weeTemplate::setFromArray call.'));

$o['one'] = 'value';
$this->isEqual(array('one' => 'value'), $o->aData,
	_WT('weeTemplate::aData should contain the variable/value pair passed to weeTemplate::offsetSet when called with two arguments.'));

$o->setFromArray(array('two' => 2, 'three' => 3));
$this->isEqual(array('one' => 'value', 'two' => 2, 'three' => 3), $o->aData,
	_WT('weeTemplate::aData should contain the array of variable/value pairs passed to weeTemplate::setFromArray when called with one argument.'));

$o->setFromArray(array('one' => 'another_value'));
$this->isEqual(array('one' => 'another_value', 'two' => 2, 'three' => 3), $o->aData,
	_WT('weeTemplate::aData should be updated when calling weeTemplate::setFromArray with existing variables.'));
