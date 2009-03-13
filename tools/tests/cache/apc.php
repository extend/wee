<?php

if (!function_exists('apc_add'))
	$this->skip();

if (!ini_get('apc.enable_cli')) {
	echo _WT(
"--
It appears that you have APC installed but not available for the command line.
Please enable it by setting the configuration variable 'apc.enable_cli' to 1
if you wish to run the APC cache driver tests.
--
");
	$this->skip();
}

$o = new stdClass;
$o->test = 'testing 1 2 3';
$o->another = 92130;

$oCache = new weeAPC;

$oCache->clear();

apc_store('wee_bool', serialize(true));
apc_store('wee_int', serialize(42));
apc_store('wee_string', serialize('This is a test string.'));
apc_store('wee_array', serialize(array('key' => 'value')));
apc_store('wee_deeparray', serialize(array('sub' => array('key' => 'value'))));
apc_store('wee_object', serialize($o));

try {
	$oCache->create('wee_string', 'Overwriting...');
	$this->fail(_WT('The method weeCache::create should fail if the key exists.'));
} catch (CacheException $e) {
}

try {
	$oCache->store('wee_string', 'Overwriting...');
} catch (CacheException $e) {
	$this->fail(_WT('The method weeCache::store should not fail if the key exists.'));
}

$this->isTrue(isset($oCache['wee_bool']), _WT('The key "wee_bool" should be defined.'));
$this->isTrue(isset($oCache['wee_int']), _WT('The key "wee_int" should be defined.'));
$this->isTrue(isset($oCache['wee_string']), _WT('The key "wee_string" should be defined.'));
$this->isTrue(isset($oCache['wee_array']), _WT('The key "wee_array" should be defined.'));
$this->isTrue(isset($oCache['wee_deeparray']), _WT('The key "wee_deeparray" should be defined.'));
$this->isTrue(isset($oCache['wee_object']), _WT('The key "wee_object" should be defined.'));
$this->isFalse(isset($oCache['wee_undefined']), _WT('The key "wee_undefined" should not have been defined.'));

unset($oCache['wee_int']);
$oCache['wee_int'] = 462;

$this->isIdentical(true, $oCache['wee_bool'], _WT('The value for the key "wee_bool" is incorrect.'));
$this->isIdentical(462, $oCache['wee_int'], _WT('The value for the key "wee_int" is incorrect.'));
$this->isIdentical('Overwriting...', $oCache['wee_string'], _WT('The value for the key "wee_string" is incorrect.'));
$this->isIdentical(array('key' => 'value'), $oCache['wee_array'], _WT('The value for the key "wee_array" is incorrect.'));
$this->isIdentical(array('sub' => array('key' => 'value')), $oCache['wee_deeparray'], _WT('The value for the key "wee_deeparray" is incorrect.'));
$this->isEqual($o, $oCache['wee_object'], _WT('The value for the key "wee_object" is incorrect.'));

try {
	$m = $oCache['wee_undefined'];
	$this->fail(_WT('A value was found for an undefined key.'));
} catch (CacheException $e) {
}

$a = $oCache->getMulti(array('wee_int', 'wee_array', 'wee_undefined'));
$this->isIdentical(462, $a['wee_int'], _WT('The value for the key "wee_int" is incorrect.'));
$this->isIdentical(array('key' => 'value'), $a['wee_array'], _WT('The value for the key "wee_array" is incorrect.'));
$this->isFalse(isset($a['wee_undefined']), _WT('A value was found for an undefined key.'));

unset($oCache['wee_bool']);
$this->isFalse(isset($oCache['wee_bool']), _WT('The key "wee_bool" should have been removed.'));

$oCache->clear();
$this->isFalse(isset($oCache['wee_int']), _WT('The key "wee_int" should have been cleared.'));
$this->isFalse(isset($oCache['wee_string']), _WT('The key "wee_string" should have been cleared.'));
$this->isFalse(isset($oCache['wee_array']), _WT('The key "wee_array" should have been cleared.'));
$this->isFalse(isset($oCache['wee_deeparray']), _WT('The key "wee_deeparray" should have been cleared.'));
$this->isFalse(isset($oCache['wee_object']), _WT('The key "wee_object" should have been cleared.'));
