<?php

$sDirname	= ROOT_PATH . 'app/tmp/base';
$sDirname2	= $sDirname . '/tmp2';
$sDirname3	= $sDirname2 . '/tmp3';
$sFilename	= $sDirname . '/file.txt';
$sFilename2	= $sDirname2 . '/file.txt';
$sFilename3	= $sDirname3 . '/file.txt';

!file_exists($sDirname) or burn('UnexpectedValueException',
	_WT('The test could not be run because the test directory already exist. You should run "make fclean".'));

@mkdir($sDirname3, 0755, true) or burn('UnexpectedValueException',
	_WT('The test could not be run because the test directory could not be created.'));

try {
	rmdir_recursive($sDirname . '/NOT_FOUND');
	$this->fail(_WT('rmdir_recursive should trigger an error when trying to remove a file which does not exist.'));
} catch (ErrorException $e) {}

touch($sFilename);
touch($sFilename2);
touch($sFilename3);

if (!defined('WEE_ON_WINDOWS')) {
	// These tests are not compatible with Windows

	chmod($sDirname, 0000);
	try {
		rmdir_recursive($sDirname);
		$this->fail(_WT('rmdir_recursive should trigger an error when trying to remove a directory without appropriate privileges.'));
	} catch (ErrorException $e) {}
	chmod($sDirname, 0755);
}

try {
	rmdir_recursive($sDirname, true);
} catch (ErrorException $e) {
	$this->fail(_WT('rmdir_recursive should not trigger an error when removing the contents of a directory with appropriate privileges.'));
}

$this->isTrue(file_exists($sDirname),
	_WT('rmdir_recursive should not remove the given directory itself when its second argument is true.'));

$o = new RecursiveDirectoryIterator($sDirname);

$this->isTrue(count(iterator_to_array($o->getChildren())) == 0,
	_WT('rmdir_recursive should empty the directory that was passed to it.'));

touch($sFilename);

try {
	rmdir_recursive($sDirname);
} catch (ErrorException $e) {
	$this->fail(_WT('rmdir_recursive should not trigger an error when removing a directory with appropriate privileges.'));
}

$this->isFalse(file_exists($sDirname),
	_WT('rmdir_recursive should remove the given directory itself when its second argument is false.'));
