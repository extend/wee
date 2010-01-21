<?php // encoding: utf-8

class weeTemplate_test extends weeTemplate {
	public $aData = array();

	public function __construct() {}
}

$o = new weeTemplate_test;
$o->setMIMEType('text/html');

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
