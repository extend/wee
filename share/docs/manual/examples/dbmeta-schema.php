<?php

try {
	$oSchema = $oDb->meta()->schema('some_schema');
} catch (UnexpectedValueException $e) {
	// The schema "some_schema" does not exist in the database.
}
