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

try {
	new weeURLValidator(new stdClass);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is an object which is neither an instance of Printable nor an object castable to string.'));
} catch (DomainException $e) {}

try {
	new weeURLValidator(true);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is a boolean.'));
} catch (DomainException $e) {}

try {
	new weeURLValidator(null);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is null.'));
} catch (DomainException $e) {}

try {
	new weeURLValidator(array());
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is an array.'));
} catch (DomainException $e) {}

try {
	new weeURLValidator(42);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is an integer.'));
} catch (DomainException $e) {}

try {
	new weeURLValidator(42.42);
	$this->fail(_WT('weeURLValidator should throw a DomainException when the value is a float.'));
} catch (DomainException $e) {}

try {
	new weeURLValidator('http://example.com');
} catch (DomainException $e) {
	$this->fail(_WT('weeURLValidator should not throw a DomainException when the value is a string.'));
}

try {
	new weeURLValidator(new PrintableInput_testURLValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeURLValidator should not throw a DomainException when the value is an instance of Printable.'));
}

try {
	new weeURLValidator(new CastableInput_testURLValidator);
} catch (DomainException $e) {
	$this->fail(_WT('weeURLValidator should not throw a DomainException when the value is an object castable to string.'));
}

$this->isTrue(weeURLValidator::test('http://example.com'),
	'weeURIValidator fails to validate "http://example.com".');
