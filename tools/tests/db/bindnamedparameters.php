<?php

require('pgsql/connect.php.inc');

// Test the method weeDatabase::bindNamedParameters

try {
	$oDb->bindNamedParameters(array(
		'SELECT * FROM table WHERE my_string=:my_string AND my_int=:my_int AND my_float=:my_float LIMIT 1',
		array('my_string' => 'eggs are good, yup, yum', 'my_int' => 42, 'my_float' => 2008.2008),
	));
} catch (DatabaseException $e) {
	$this->fail(_WT('weeDatabase::bindNamedParameters should not throw an exception for a random value.'));
}

try {
	$oDb->bindNamedParameters(array(
		'SELECT * FROM table WHERE my_field=:my_value LIMIT 1',
		array('my_value' => null),
	));
} catch (DatabaseException $e) {
	$this->fail(_WT('weeDatabase::bindNamedParameters should not throw an exception for null values.'));
}
