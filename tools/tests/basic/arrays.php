<?php

// PHP has weird behaviors with ArrayAccess. All array_* functions
// do not work with them. This unit test checks the behavior of a
// few functions and will fail if PHP changes something

// You can use these tests as a reference of these behaviors

$a = array('egg' => 'chicken', 'null' => null);

// weeDataHolder uses array_key_exists in offsetExists
$o1 = new weeDataHolder($a);

// We want to test with isset in offsetExists too
class weeDataHolder_testWithIsset extends weeDataHolder {
	public function offsetExists($sKey) {
		return isset($this->aData[$sKey]);
	}
}
$o2 = new weeDataHolder_testWithIsset($a);

// is_array
$this->isTrue(is_array($a), 'is_array $a');
$this->isFalse(is_array($o1), 'is_array $o1');
$this->isFalse(is_array($o2), 'is_array $o2');

// Same behavior with empty
$this->isFalse(empty($a['egg']), 'empty $a egg');
$this->isFalse(empty($o1['egg']), 'empty $o1 egg');
$this->isFalse(empty($o2['egg']), 'empty $o2 egg');

$this->isTrue(empty($a['null']), 'empty $a null');
$this->isTrue(empty($o1['null']), 'empty $o1 null');
$this->isTrue(empty($o2['null']), 'empty $o2 null');

// Same behavior with isset on a string
$this->isTrue(isset($a['egg']), 'isset $a egg');
$this->isTrue(isset($o1['egg']), 'isset $o1 egg');
$this->isTrue(isset($o2['egg']), 'isset $o2 egg');

// Different behavior with isset on a null value
// Reason: $o1->offsetExists uses array_key_exists while $o2 uses isset
// isset on an ArrayAccess object uses the offsetExists method to
// determine if a value is set or not
$this->isFalse(isset($a['null']), 'isset $a null');
$this->isTrue(isset($o1['null']), 'isset $o1 null');
$this->isFalse(isset($o2['null']), 'isset $o2 null');

// Different behavior with array_key_exists (does not work with ArrayAccess)
$this->isTrue(array_key_exists('egg', $a), 'array_key_exists $a egg');
$this->isFalse(array_key_exists('egg', $o1), 'array_key_exists $o1 egg');
$this->isFalse(array_key_exists('egg', $o2), 'array_key_exists $o2 egg');

$this->isTrue(array_key_exists('null', $a), 'array_key_exists $a null');
$this->isFalse(array_key_exists('null', $o1), 'array_key_exists $o1 null');
$this->isFalse(array_key_exists('null', $o2), 'array_key_exists $o2 null');

// As we can see here, if we need to use array_key_exists on both an
// array and an ArrayAccess object, we need to do the following:
// - have the offsetExists method of the object test the existence
//   using the array_key_exists function
// - do the following test with $m our variable and $sKey our key:
//      ((is_array($m) && array_key_exists($sKey, $m))
//       || (!is_array($m) && isset($m[$sKey])))
// isset will use the offsetExists to check the existence of the
// value, and our offsetExists uses the array_key_exists function

function basic_arrays_real_array_key_exist($sKey, $m)
{
	return ((is_array($m) && array_key_exists($sKey, $m))
		|| (!is_array($m) && isset($m[$sKey])));
}

$this->isTrue(basic_arrays_real_array_key_exist('egg', $a), 'real_array_key_exists $a egg');
$this->isTrue(basic_arrays_real_array_key_exist('egg', $o1), 'real_array_key_exists $a egg');

$this->isTrue(basic_arrays_real_array_key_exist('null', $a), 'real_array_key_exists $a null');
$this->isTrue(basic_arrays_real_array_key_exist('null', $o1), 'real_array_key_exists $a null');

$this->isFalse(basic_arrays_real_array_key_exist('doesntexist', $a), 'real_array_key_exists $a doesntexist');
$this->isFalse(basic_arrays_real_array_key_exist('doesntexist', $o1), 'real_array_key_exists $a doesntexist');

// To check if a key do NOT exist, it becomes the following:
//      ((is_array($m) && !array_key_exists($sKey, $m))
//       || (!is_array($m) && !isset($m[$sKey])))

function basic_arrays_real_array_key_doesnt_exist($sKey, $m)
{
	return ((is_array($m) && !array_key_exists($sKey, $m))
		|| (!is_array($m) && !isset($m[$sKey])));
}

$this->isFalse(basic_arrays_real_array_key_doesnt_exist('egg', $a), 'real_array_key_doesnt_exists $a egg');
$this->isFalse(basic_arrays_real_array_key_doesnt_exist('egg', $o1), 'real_array_key_doesnt_exists $a egg');

$this->isFalse(basic_arrays_real_array_key_doesnt_exist('null', $a), 'real_array_key_doesnt_exists $a null');
$this->isFalse(basic_arrays_real_array_key_doesnt_exist('null', $o1), 'real_array_key_doesnt_exists $a null');

$this->isTrue(basic_arrays_real_array_key_doesnt_exist('doesntexist', $a), 'real_array_key_doesnt_exists $a doesntexist');
$this->isTrue(basic_arrays_real_array_key_doesnt_exist('doesntexist', $o1), 'real_array_key_doesnt_exists $a doesntexist');
