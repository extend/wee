<?php

try {
	new weeMySQLResult(fopen('php://memory', 'r'));
	$this->fail(_WT('weeMySQLResult should throw an InvalidArgumentException when the given resource is not a mysql result.'));
} catch (InvalidArgumentException $e) {}
