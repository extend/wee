<?php

class test_weeModel extends weeModel {}

$aData 			= array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4');
$aData2			= array('key5' => 'value5', 'key6' => 'value6');
$aExpectedData	= array('key5' => 'value5', 'key6' => 'value6', 'key2' => 'value2', 'key3' => 'newvalue3', 'key4' => 'value4');

$o = new test_weeModel($aData);

$this->isTrue($o->valid(),
	_WT('weeModel::valid should return true, the $aData array should have an element.'));
$this->isEqual(key($aData), $o->key(), 
	_WT('weeModel::key should return the key of the current element.'));
$this->isEqual($aData[key($aData)], $o->current(),
	_WT('weeModel::current should return the value of the current element.'));

$o->next();

$this->isTrue($o->valid(),
	_WT('weeModel::valid should return true, the $aData array should have elements.'));

$this->isNotEqual($o->key(), key($aData), 
	sprintf(_WT('weeModel::key should not return "%s" but "key2" instead.'), $o->key()));
$this->isNotEqual($o->current(), $aData[key($aData)], 
	sprintf(_WT('weeModel::current should not return "%s" but "value2" instead.'), $o->current()));

$this->isTrue($o->offsetExists($o->key()),
	sprintf(_WT('weeModel::offsetExists the offset "%s" should exists.'), $o->key()));
$this->isFalse($o->offsetExists('badoffset'),
	_WT('weeModel::offsetExists the offset badoffset should not exists.'));

try {
	$this->isEqual($aData['key1'], $o->offsetGet('key1'),
		_WT('weeModel::offsetGet should return the value of the element of the given key.'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeModel::offsetGet should not throw an InvalidArgumentException the offset "key1" should exists.'));
}

try {
	$o->offsetGet('badoffset');
	$this->fail(_WT('weeModel::offsetGet should throw an InvalidArgumentException the offset "badoffset" should not exists.'));
} catch (InvalidArgumentException $e) {}

try {
	$o->offsetSet('key3', 'newvalue3');

	$this->isEqual('newvalue3', $o->offsetGet('key3'),
		_WT('weeModel::offsetGet should be able to return the value of an element added through weeModel::offsetSet.'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeModel::offsetGet should not throw an InvalidArgumentException the offset "key3" should exist.'));
}

try {
	$o->offsetUnset('key1');
	$o->offsetGet('key1');
	$this->fail(_WT('weeModel::offsetGet should throw an InvalidArgumentException the offset "key1" should not exists.'));
} catch (InvalidArgumentException $e) {}

$o->rewind();
$this->isTrue($o->valid(),
	_WT('weeModel::valid should return true, the $aData array should have elements.'));
$this->isEqual('value2', $o->current(),
	_WT('weeModel::current should return the value of the first element of the model after a call of weeModel::rewind.'));

try {
	$o->setFromArray($aData2);
	$this->isEqual($aExpectedData, $o->toArray(),
		_WT('weeModel::toArray the array is different from the expected array.'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeModel::setFromArray should not throw an InvalidArgumentException.'));
}

try {
	$o->setFromArray(42);
	$this->fail(_WT('weeModel::setFromArray should throw an InvalidArgumentException.'));
} catch (InvalidArgumentException $e) {}
