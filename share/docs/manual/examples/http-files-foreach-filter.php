<?php

$oUploads = new weeUploads;

foreach ($oUploads->filter('myfiles') as $oFile)
	doSomething($oFile);
