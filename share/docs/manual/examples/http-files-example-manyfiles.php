<?php

$oUploads = new weeUploads;

foreach ($oUploads as $oFile) {
	if ($oFile->isOK())
		$oFile->moveTo('/path/to/destination/folder');
	else
		echo $oFile->getError();
}
