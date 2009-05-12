<?php

// Initialization

if (!defined('FORM_PATH'))
	define('FORM_PATH', dirname(__FILE__) . '/form/');

// Test

$oForm = new weeForm('options', 'update');
$oHelper = $oForm->helper('weeFormOptionsHelper', 'choice');

$aOptions = array(
	array('label' => 'test 1', 'value' => '39'),
	array('label' => 'test 2', 'value' => '40'),
	array('label' => 'test 3', 'value' => '41'),
	array('label' => 'test 4', 'value' => '43'),
	array('name' => 'group', 'children' => array(array('label' => 'test 5', 'value' => '44'))),
);

$oHelper->addOptions($aOptions);
$oDupeHelper = $oForm->helper('weeFormOptionsHelper', 'dupe');
$oDupeHelper->addOptions($aOptions);

try {
	$oForm->validate(array('choice' => 42));
	$this->fail(_WT('Form validation should have failed.'));
} catch (FormValidationException $e) {
}

$oHelper->addOption(array('label' => 'test 42', 'value' => '42'));

try {
	$oForm->validate(array('choice' => 42));
} catch (FormValidationException $e) {
	$this->fail(_WT('Form validation should have succeeded.'));
}

$oHelper->select('39');
$this->isTrue($oHelper->isSelected(39), _WT('Only 39 should have been selected.'));
$this->isFalse($oHelper->isSelected(40), _WT('Only 39 should have been selected.'));
$this->isFalse($oHelper->isSelected(41), _WT('Only 39 should have been selected.'));
$this->isFalse($oHelper->isSelected(42), _WT('Only 39 should have been selected.'));
$this->isFalse($oHelper->isSelected(43), _WT('Only 39 should have been selected.'));
$this->isFalse($oHelper->isSelected(44), _WT('Only 39 should have been selected.'));

$oDupeHelper->select('40');
$oHelper->selectNone();
$this->isTrue($oDupeHelper->isSelected(40), _WT('The dupe item should still be selected.'));

$oHelper->select(array('41', '44'));
$this->isFalse($oHelper->isSelected(39), _WT('Only 41 and 44 should have been selected.'));
$this->isFalse($oHelper->isSelected(40), _WT('Only 41 and 44 should have been selected.'));
$this->isTrue($oHelper->isSelected(41), _WT('Only 41 and 44 should have been selected.'));
$this->isFalse($oHelper->isSelected(42), _WT('Only 41 and 44 should have been selected.'));
$this->isFalse($oHelper->isSelected(43), _WT('Only 41 and 44 should have been selected.'));
$this->isTrue($oHelper->isSelected(44), _WT('Only 41 and 44 should have been selected.'));

$oHelper->selectOne('43');
$this->isFalse($oHelper->isSelected(39), _WT('Only 43 should have been selected.'));
$this->isFalse($oHelper->isSelected(40), _WT('Only 43 should have been selected.'));
$this->isFalse($oHelper->isSelected(41), _WT('Only 43 should have been selected.'));
$this->isFalse($oHelper->isSelected(42), _WT('Only 43 should have been selected.'));
$this->isTrue($oHelper->isSelected(43), _WT('Only 43 should have been selected.'));
$this->isFalse($oHelper->isSelected(44), _WT('Only 43 should have been selected.'));

$oForm		= new weeForm('options', 'update');
$oHelper	= $oForm->helper('weeFormOptionsHelper', 'choice');

class Printable_concrete implements Printable
{
	private $s;

	public function __construct($s)
	{
		$this->s = $s;
	}

	public function toString()
	{
		return $this->s;
	}
}

$oHelper->addOption('string');
$this->isTrue($oHelper->isInOptions('string'), _WT('Adding an option as a string does not work.'));

$oHelper->addOption(new Printable_concrete('printable'));
$this->isTrue($oHelper->isInOptions('printable'), _WT('Adding an option as a printable object does not work.'));

$oHelper->select('string');
$this->isTrue($oHelper->isSelected('string'), _WT('Selecting an option which has been added as a string does not work.'));

$oHelper->selectOne('printable');
$this->isTrue($oHelper->isSelected('printable'), _WT('Selecting an option which has been added as a printable object does not work.'));
