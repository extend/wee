<?php

try {
	new weePgSQLResult(fopen('php://memory', 'r'));
	$this->fail(_WT('weePgSQLResult should throw an InvalidArgumentException when the given resource is not a pgsql result.'));
} catch (InvalidArgumentException $e) {}
