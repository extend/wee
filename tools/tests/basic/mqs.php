<?php

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

$this->isIdentical($aTest, $aWanted, 'mqs is not stripping slashes properly.');
