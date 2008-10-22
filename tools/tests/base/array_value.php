<?php

$aData = array('key' => 'value', 'key2' => 'value2', 'key3' => 'value3');
$sValueIfNotSet = 'valueifnotset';

$sValue = array_value($aData, 'key');
$this->isEqual($sValue, $aData['key'],
		sprintf(_('array_value should return "%s" got "%s" instead.'), $aData['key'], $sValue));

$sValue = array_value($aData, 'key', $sValueIfNotSet);
$this->isEqual($sValue, $aData['key'],
		sprintf(_('array_value should return "%s" got "%s" instead.'), $aData['key'], $sValue));

$sValue = array_value($aData, 'badkey');
$this->isNull($sValue, sprintf(_('array_value should return null got "%s" instead.'), $sValue));

$sValue = array_value($aData, 'badkey', $sValueIfNotSet);
$this->isEqual($sValue, $sValueIfNotSet,
		sprintf(_('array_value should return "%s" got "%s" instead.'), $sValueIfNotSet, $sValue));
