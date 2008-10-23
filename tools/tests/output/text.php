<?php

class weeTextOutput_test extends weeTextOutput {
	public function __construct() {}
}

$o = new weeTextOutput_test;

// weeTextOutput::encode

$this->isEqual('win', $o->encode('win'),
	_('weeTextOutput::encode should return its argument when it does not contain any NUL character.'));

$this->isEqual('', $o->encode("\0"),
	_('weeTextOutput::encode should return its argument rid of any NUL characters.'));

// weeTextOutput::decode

try {
	$o->decode("\0");
	$this->fail(_('weeTextOutput::decode should throw an InvalidArgumentException if the value to decode contain any NUL character.'));
} catch (InvalidArgumentException $e) {}

$this->isEqual('win', $o->decode('win'),
	_('weeTextOutput::decode should always return its argument.'));
