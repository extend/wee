<?php

try {
	// Initialize your test here

	if ($iStep == 1) {
		runStep1();
	} elseif ($iStep == 2) {
		runStep2();
	// More elseif if needed
	} else {
		runStepN();
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
