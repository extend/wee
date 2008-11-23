<?php

class PrintableInput_testURLValidator implements Printable {
	public function toString() {
		return 'http://example.com';
	}
}

class CastableInput_testURLValidator {
	public function __toString() {
		return 'http://example.com';
	}
}

// weeURLValidator should throw a DomainException when the value to validate is not a string or an instance of Printable or an object castable to string.

$o = new weeURLValidator;

try {
	$o->setValue(new stdClass);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	$o->setValue(true);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	$o->setValue(null);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	$o->setValue(array());
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	$o->setValue(42);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is an integer.'));
} catch (DomainException $e) {}

try {
	$o->setValue(42.42);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is a float.'));
} catch (DomainException $e) {}

try {
	$o->setValue('http://example.com');
} catch (DomainException $e) {
	$this->fail(_WT('weeURLValidator should not throw a DomainException when the value is a string.'));
}

try {
	$o->setValue(new PrintableInput_testURLValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeURLValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	$o->setValue(new CastableInput_testURLValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeURLValidator should not throw a DomainException when the value is an object castable to string.'));
}

$this->isTrue(weeURLValidator::test('http://example.com'),
	'weeURIValidator fails to validate "http://example.com".');
