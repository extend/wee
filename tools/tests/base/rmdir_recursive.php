<?php

$sDirname	= ROOT_PATH . 'app/tmp/base';
$sDirname2	= ROOT_PATH . 'app/tmp/base/tmp2';
$sDirname3	= ROOT_PATH . 'app/tmp/base/tmp2/tmp3';
$sFilename	= $sDirname . '/file.txt';
$sFilename2	= $sDirname2 . '/file.txt';
$sFilename3	= $sDirname3 . '/file.txt';

$iRet = @mkdir($sDirname, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the directory %s.'), $sDirname));

$iRet = @mkdir($sDirname2, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the directory %s.'), $sDirname2));

$iRet = @mkdir($sDirname3, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the directory %s.'), $sDirname3));

try {
	rmdir_recursive($sFilename);
	$this->fail(sprintf(_WT('rmdir_recursive should throw a FileNotFoundException when trying to delete %s'), $sFilename));
} catch (FileNotFoundException $e) {}

touch($sFilename);
touch($sFilename2);
touch($sFilename3);

if (!defined('WEE_ON_WINDOWS')) {
	// These tests are not compatible with Windows

	chmod($sDirname, 0000);
	try {
		rmdir_recursive($sDirname);
		$this->fail(sprintf(_WT('rmdir_recursive should throw a NotPermittedException when trying to open %s.'), $sDirname));
	} catch (NotPermittedException $e) {}
	chmod($sDirname, 0755);
}

try {
	rmdir_recursive($sDirname, true);
} catch (NotPermittedException $e) {
	$this->fail(sprintf(_WT('rmdir_recursive should not throw a NotPermittedException when trying to delete the contents of %s.'), $sDirname));
}

try {
	rmdir_recursive($sDirname);
} catch (NotPermittedException $e) {
	$this->fail(sprintf(_WT('rmdir_recursive should not throw a NotPermittedException when trying to delete %s.'), $sDirname));
}
