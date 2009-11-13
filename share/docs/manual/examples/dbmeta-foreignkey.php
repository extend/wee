<?php

try {
	$oForeignKey = $oTable->foreignKey('some_foreign_key');
} catch (UnexpectedValueException $e) {
	// the foreign key "some_foreign_key" does not exist in the table.
}
