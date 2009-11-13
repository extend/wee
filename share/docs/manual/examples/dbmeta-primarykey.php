<?php

try {
	$oPrimaryKey = $oTable->primaryKey();
} catch (IllegalStateException $e) {
	// The table does not have a primary key.
}
