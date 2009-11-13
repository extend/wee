<?php

$oUploads = new weeUploads;

if ($oUploads->exists('myfile')) {
	$oFile = $oUploads->fetch('myfile');
	doSomething($oFile);
}
