<?php

class weeLaTeXOutput_test extends weeLaTeXOutput {
	public function __construct() {}
}

$o = new weeLaTeXOutput_test;

// Per latex tutorial, the following need escaping: # $ % & ~ _ ^ \ { }
$aTable = array(
	'#'		=> '\\#',
	'$'		=> '\\$',
	'%'		=> '\\%',
	'&'		=> '\\&',
	'~'		=> '\\~',
	'_'		=> '\\_',
	'^'		=> '\\^',
	'\\'	=> '\\textbackslash ',
	'{'		=> '\\{',
	'}'		=> '\\}'
);

$this->isEqual('win', $o->encode('win'),
	_('weeLaTeXOutput::encode should return its argument untouched if it does not contain any special character.'));

$this->isEqual('win', $o->decode('win'),
	_('weeLaTeXOutput::decode should return its argument untouched if it does not contain any LaTeX entity.'));

foreach ($aTable as $sDecoded => $sEncoded) {
	$this->isEqual($sEncoded, $o->encode($sDecoded),
		_('weeLateXOutput::encode should return the expected encoded value.'));

	$this->isEqual($sDecoded, $o->decode($sEncoded),
		_('weeLaTeXOutput::decode should return the expected decoded value.'));
}
