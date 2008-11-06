<?php

$aData = array('key' => 'value', 'key2' => 'value2', 'key3' => 'value3');
$sValueIfNotSet = 'valueifnotset';

$sValue = array_value($aData, 'key');
$this->isEqual($sValue, $aData['key'],
		sprintf(_WT('array_value should return "%s" got "%s" instead.'), $aData['key'], $sValue));

$sValue = array_value($aData, 'key', $sValueIfNotSet);
$this->isEqual($sValue, $aData['key'],
		sprintf(_WT('array_value should return "%s" got "%s" instead.'), $aData['key'], $sValue));

$sValue = array_value($aData, 'badkey');
$this->isNull($sValue, sprintf(_WT('array_value should return null got "%s" instead.'), $sValue));

$sValue = array_value($aData, 'badkey', $sValueIfNotSet);
$this->isEqual($sValue, $sValueIfNotSet,
		sprintf(_WT('array_value should return "%s" got "%s" instead.'), $sValueIfNotSet, $sValue));
