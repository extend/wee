<?php

if (!get_magic_quotes_gpc())
	$this->skip();

$aTest = array(
	"test\\'d",
	'normal',
	"loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong\\'",
	"\\'short",
	"\\'",
	"''\\'''''''\\'''''''''''\\''''''\\''''''''''''\\'''",
	"'\\'\\'\\'\\'",
	array (
		"also recursive! \\'",
		"or is it\\?",
		"\\\\\\\\\\\\\\\\\\\\\\\\",
	),
);

$aWanted = array(
	"test'd",
	'normal',
	"loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong'",
	"'short",
	"'",
	"'''''''''''''''''''''''''''''''''''''''''",
	"'''''",
	array (
		"also recursive! '",
		"or is it?",
		"\\\\\\\\\\\\",
	),
);

mqs($aTest);

$this->isIdentical($aWanted, $aTest, _WT('mqs is not stripping slashes properly.'));
