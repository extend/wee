<?php

class test_weeModel extends weeModel {}

$aData 			= array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4');
$aData2			= array('key5' => 'value5', 'key6' => 'value6');
$aExpectedData	= array('key5' => 'value5', 'key6' => 'value6', 'key2' => 'value2', 'key3' => 'newvalue3', 'key4' => 'value4');

$o = new test_weeModel($aData);

$this->isTrue($o->valid(),
		_('weeModel::valid should return true, the $aData array should have an element.'));
$this->isEqual($o->key(), key($aData), 
		sprintf(_('weeModel::key should return "%s" got "%s" instead.'), key($aData), $o->key()));
$this->isEqual($o->current(), $aData[key($aData)], 
		sprintf(_('weeModel::current should return "%s" got "%s" instead.'), $aData[key($aData)], $o->current()));

$o->next();

$this->isTrue($o->valid(),
		_('weeModel::valid should return true, the $aData array should have elements.'));

$this->isNotEqual($o->key(), key($aData), 
		sprintf(_('weeModel::key should not return "%s" but "key2" instead.'), $o->key()));
$this->isNotEqual($o->current(), $aData[key($aData)], 
		sprintf(_('weeModel::current should not return "%s" but "value2" instead.'), $o->current()));

$this->isTrue($o->offsetExists($o->key()),
		sprintf(_('weeModel::offsetExists the offset "%s" should exists.'), $o->key()));
$this->isFalse($o->offsetExists('badoffset'),
		_('weeModel::offsetExists the offset badoffset should not exists.'));

try {
	$sValue = $o->offsetGet('key1');
	$this->isEqual($sValue, $aData['key1'], 
		sprintf(_('weeModel::offsetGet should return "%s" got "%s" instead.'), $aData['key1'], $sValue));
} catch (InvalidArgumentException $e) {
	$this->fail(_('weeModel::offsetGet should not throw an InvalidArgumentException the offset "key1" should exists.'));
}

try {
	$o->offsetGet('badoffset');
	$this->fail(_('weeModel::offsetGet should throw an InvalidArgumentException the offset "badoffset" should not exists.'));
} catch (InvalidArgumentException $e) {}

try {
	$o->offsetSet('key3', 'newvalue3');

	$sValue = $o->offsetGet('key3');
	$this->isEqual($sValue, 'newvalue3', 
		sprintf(_('weeModel::offsetGet should return "newvalue3" got "%s" instead.'), $sValue));
} catch (InvalidArgumentException $e) {
	$this->fail(_('weeModel::offsetGet should not throw an InvalidArgumentException the offset "key1" should exists.'));
}

try {
	$o->offsetUnset('key1');
	$o->offsetGet('key1');
	$this->fail(_('weeModel::offsetGet should throw an InvalidArgumentException the offset "key1" should not exists.'));
} catch (InvalidArgumentException $e) {}

$o->rewind();
$this->isTrue($o->valid(),
		_('weeModel::valid should return true, the $aData array should have elements.'));
$this->isEqual($o->current(), 'value2', 
		sprintf(_('weeModel::current should return "value2" got "%s" instead.'), $o->current()));

try {
	$o->setFromArray($aData2);
	$this->isEqual($o->toArray(), $aExpectedData,
		_('weeModel::toArray the array is different from the expected array.'));
} catch (InvalidArgumentException $e) {
	$this->fail(_('weeModel::setFromArray should not throw an InvalidArgumentException.'));
}

try {
	$o->setFromArray(42);
	$this->fail(_('weeModel::setFromArray should throw an InvalidArgumentException.'));
} catch (InvalidArgumentException $e) {}
