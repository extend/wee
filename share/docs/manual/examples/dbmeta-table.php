<?php

try {
	$oTable = $oDb->meta()->table('some_table');
} catch (UnexpectedValueException $e) {
	// The table "some_table" does not exist in the database
}
