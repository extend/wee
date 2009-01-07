<?php

// _T

try {
	_T();
	$this->fail(_WT('_T should throw an InvalidArgumentException when called without any argument.'));
} catch (InvalidArgumentException $e) {}

try {
	_T('singular', 'plural');
	$this->fail(_WT('_T should throw an InvalidArgumentException when called with 2 arguments.'));
} catch (InvalidArgumentException $e) {}

try {
	_T('string');
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('_T should not throw an InvalidArgumentException when called with one argument.'));
}

try {
	_T('singular', 'plural', 42);
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('_T should not throw an InvalidArgumentException when called with 3 arguments.'));
}

$this->isEqual('not_translated', _T('not_translated'),
	_WT('_T failed to return the original string when it is not translated.'));

$this->isEqual('singular_not_translated', _T('singular_not_translated', 'plural_not_translated', 1),
	_WT('_T failed to return the original singular string when it is not translated and the third argument is 1.'));

$this->isEqual('plural_not_translated', _T('singular_not_translated', 'plural_not_translated', 2),
	_WT('_T failed to return the original plural string when it is not translated and the third argument is greater than 1.'));

// _WT

try {
	_WT();
	$this->fail(_WT('_WT should throw an InvalidArgumentException when called without any argument.'));
} catch (InvalidArgumentException $e) {}

try {
	_WT('singular', 'plural');
	$this->fail(_WT('_WT should throw an InvalidArgumentException when called with 2 arguments.'));
} catch (InvalidArgumentException $e) {}

try {
	_WT('string');
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('_WT should not throw an InvalidArgumentException when called with one argument.'));
}

try {
	_WT('singular', 'plural', 42);
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('_WT should not throw an InvalidArgumentException when called with 3 arguments.'));
}

$this->isEqual('not_translated', _WT('not_translated'),
	_WT('_WT failed to return the original string when it is not translated.'));

$this->isEqual('singular_not_translated', _WT('singular_not_translated', 'plural_not_translated', 1),
	_WT('_WT failed to return the original singular string when it is not translated and the third argument is 1.'));

$this->isEqual('plural_not_translated', _WT('singular_not_translated', 'plural_not_translated', 2),
	_WT('_WT failed to return the original plural string when it is not translated and the third argument is greater than 1.'));
