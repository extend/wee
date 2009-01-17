<?php

weeAutoload::addPath(dirname(__FILE__) . '/classes.inc');
weeAutoload::loadClass('weeAutoload_test');

$this->isTrue(class_exists('weeAutoload_test', false),
	_WT('weeAutoload::loadClass failed to load the given class even though the current directory is in the autoload paths.'));
