<?php

class weeFormValidator_test extends weeFormValidator {
	public function validate() {
		parent::validate();
	}

	protected function isValidInput($mInput) {
		return true;
	}
}

$o = new weeFormValidator_test(42);

// weeFormValidator::validate should throw an IllegalStateException when the validator has not been associated with a form widget.

try {
	$o->validate();
	$this->fail(_('weeFormValidator::validate should throw an IllegalStateException when the validator has not been associated with a form widget.'));
} catch (IllegalStateException $e) {}

$o->setFormData(simplexml_load_string('<widget/>'), array());

try {
	$o->validate();
} catch (IllegalStateException $e) {
	$this->fail(_('weeFormValidator::validate should not throw an IllegalStateException when the validator has been associated with a form widget.'));
}

// weeFormValidator::setFormData should throw an IllegalStateException when the validator has already been associated with a form widget.

try {
	$o->setFormData(simplexml_load_string('<widget/>'), array());
	$this->fail(_('weeFormValidator::setFormData should throw an IllegalStateException when the validator has already been associated with a form widget.'));
} catch (IllegalStateException $e) {}
