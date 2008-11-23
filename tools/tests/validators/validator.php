<?php

class weeValidator_testValidator extends weeValidator {
	public $aArgs = array();
	protected function isValidInput($bInput) {
		return $bInput;
	}
}

$o = new weeValidator_testValidator(array('answer', 42));

// weeValidator::hasError

try {
	$o->hasError();
	$this->fail(_WT('weeValidator::hasError should throw an IllegalStateException when no value has been attached to the validator.'));
} catch (IllegalStateException $e) {}

$o->setValue(false);

try {
	$o->hasError();
} catch (IllegalStateException $e) {
	$this->fail(_WT('weeValidator::hasError should not throw a DomainException when a value has been attached to the validator.'));
}

$this->isTrue($o->hasError(),
	_WT('weeValidator::hasError should return true when the attached value is not a valid input.'));

$o->setValue(true);

$this->isFalse($o->hasError(),
	_WT('weeValidator::hasError should return false when the attached value is a valid input.'));

// weeValidator::getError

try {
	$o->getError();
	$this->fail(_WT('weeValidator::getError should throw an IllegalStateException when the attached value is a valid input.'));
} catch(IllegalStateException $e) {}

$o->setValue(false);

try {
	$o->getError();
} catch (IllegalStateException $e) {
	$this->fail(_WT('weeValidator::getError should not throw an IllegalStateException when the attached value is not a valid input.'));
}

// Serialization

$this->isEqual($o->aArgs, unserialize(serialize($o))->aArgs,
	_WT('weeValidator fails to restore its arguments upon unserialization.'));
