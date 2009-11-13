<?php

// Create an item with only a label 'test'
$oHelper->addOption('test');

// Same
$oHelper->addOption(array('label' => 'test'));

// Create an item with a label 'test' and a value '42'
$oHelper->addOption(array('label' => 'test', 'value' => 42));

// Create an item and select it
$oHelper->addOption(array('label' => 'another', 'value' => 17, 'selected' => true));

// Create a lot of items at the same time with addOptions instead of addOption
$oHelper->addOptions(array(
	array('label' => 'item 1', 'value' => 1),
	array('label' => 'item 2', 'value' => 2),
	array('label' => 'item 3', 'value' => 3),
	array('label' => 'item 4', 'value' => 4),
	// ...
));

// Create a group
$oHelper->addOption(array('label' => 'this is a group', 'name' => 'group'));

// Add an item to the group
$oHelper->addOption(array('label' => 'sub-item', 'value' => 999), 'group[@label="this is a group"]');
