<?php

$oWidget = simplexml_load_string('<widget type="choice"/>');
$oHelper = new weeFormOptionsHelper($oWidget);

// The following validation should fail.

$o = new weeOptionValidator;
$o->setValue(42)->setFormData($oWidget, array());

$this->isTrue($o->hasError(),
	_WT('weeOptionValidator::hasError should return true when the value is not present in the widget options.'));

// The following validation should succeed.

$o = new weeOptionValidator;
$oHelper->addOption(42);
$o->setValue(42)->setFormData($oWidget, array());

$this->isFalse($o->hasError(),
	_WT('weeOptionValidator::hasError should return false when the value is present in the widget options.'));
