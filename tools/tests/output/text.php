<?php

$o = new weeTextEncoder;

// weeTextEncoder::encode

$this->isEqual('win', $o->encode('win'),
	_WT('weeTextEncoder::encode should return its argument when it does not contain any NUL character.'));

$this->isEqual('', $o->encode("\0"),
	_WT('weeTextEncoder::encode should return its argument rid of any NUL characters.'));

// weeTextEncoder::decode

try {
	$o->decode("\0");
	$this->fail(_WT('weeTextEncoder::decode should throw an InvalidArgumentException if the value to decode contain any NUL character.'));
} catch (InvalidArgumentException $e) {}

$this->isEqual('win', $o->decode('win'),
	_WT('weeTextEncoder::decode should always return its argument.'));
