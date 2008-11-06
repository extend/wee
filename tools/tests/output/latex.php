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
	_WT('weeLaTeXOutput::encode should return its argument untouched if it does not contain any special character.'));

$this->isEqual('win', $o->decode('win'),
	_WT('weeLaTeXOutput::decode should return its argument untouched if it does not contain any LaTeX entity.'));

foreach ($aTable as $sDecoded => $sEncoded) {
	$this->isEqual($sEncoded, $o->encode($sDecoded),
		_WT('weeLateXOutput::encode should return the expected encoded value.'));

	$this->isEqual($sDecoded, $o->decode($sEncoded),
		_WT('weeLaTeXOutput::decode should return the expected decoded value.'));
}
