<?php

$aData = array(
	'key1' => 'value1',
	'key2' => 'value2',
	'key3' => 'value3',
	'key4' => 'value4',
);

$aData2 = array('key5' => 'value5', 'key6' => 'value6');

$aExpectedData = array(
	'key5' => 'value5',
	'key6' => 'value6',
	'key2' => 'value2',
	'key3' => 'newvalue3',
	'key4' => 'value4',
);

$o = new weeDataHolder($aData);

$this->isFalse($o->offsetExists('badoffset'),
	_WT('weeDataHolder::offsetExists does not return the excepted value when the key does not exist.'));

try {
	$this->isEqual($aData['key1'], $o->offsetGet('key1'),
		_WT('weeDataHolder::offsetGet should return the value of the element of the given key.'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeDataHolder::offsetGet should not throw when the key exist.'));
}

try {
	$o->offsetGet('badoffset');
	$this->fail(_WT('weeDataHolder::offsetGet should throw when the key does not exist.'));
} catch (InvalidArgumentException $e) {}

$o->offsetSet('key3', 'newvalue3');

$this->isEqual('newvalue3', $o->offsetGet('key3'),
	_WT('weeDataHolder::offsetSet failed to update the value of the given key.'));

$o->offsetUnset('key1');

try {
	$o->offsetGet('key1');
	$this->fail(_WT('weeDataHolder::offsetGet should throw an InvalidArgumentException the offset "key1" should not exists.'));
} catch (InvalidArgumentException $e) {}

try {
	$o->setFromArray($aData2);
	$this->isEqual($aExpectedData, $o->toArray(),
		_WT('weeDataHolder::toArray the array is different from the expected array.'));
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeDataHolder::setFromArray should not throw an InvalidArgumentException.'));
}

try {
	$o->setFromArray(42);
	$this->fail(_WT('weeDataHolder::set should throw an InvalidArgumentException.'));
} catch (InvalidArgumentException $e) {}
