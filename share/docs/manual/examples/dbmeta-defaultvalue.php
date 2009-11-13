<?php

try {
	$sDefaultValue = $oColumn->defaultValue();
} catch (IllegalStateException $e) {
	// the column does not have a default value.
}
