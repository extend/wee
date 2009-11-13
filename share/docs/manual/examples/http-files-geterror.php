<?php

$oUploads = new weeUploads;

$oFile = $oUploads->fetch('myfile');
if ($oFile->isOK())
	doSomething($oFile);
else
	echo $oFile->getError();
