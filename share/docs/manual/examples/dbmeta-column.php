<?php

try {
	$oColumn = $oTable->column('some_column');
} catch (UnexpectedValueException $e) {
	// The column "some_column" does not exist in the table.
}
