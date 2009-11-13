<?php

try {
	$a = array();
	echo $a[42]; // triggers a notice
} catch (ErrorException $e) {
	echo $e; // echoes "Undefined offset:  42"
}
