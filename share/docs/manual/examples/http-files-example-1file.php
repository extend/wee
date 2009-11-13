<?php

$oUploads = new weeUploads;
$oFile = $oUploads->fetch('myfile');

if ($oFile->isOK())
	$oFile->moveTo('/path/to/destination/folder');
else
	echo $oFile->getError();
