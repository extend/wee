<?php

$this->isTrue(true, "Boolean true isn't really true?");
$this->isFalse(false, "Boolean false isn't really false?");
$this->isTrue(1 + 1 == 2, '1 + 1 != 2?');

// This will fail
$this->isTrue(false, 'Example error message.');
