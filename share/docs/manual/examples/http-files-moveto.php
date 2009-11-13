<?php

if ($oFile->fileExists('/path/to/destination/folder', 'newfilename.txt'))
	echo 'File already exists!';
else
	$oFile->moveTo('/path/to/destination/folder', 'newfilename.txt');
