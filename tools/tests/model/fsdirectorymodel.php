<?php

$sDirname	= ROOT_PATH . 'app/tmp/fsdirectorymodel';
$sDirname2	= $sDirname . '/tmp2';
$sDirname3	= $sDirname2 . '/tmp3';
$sFilename	= $sDirname . '/file.txt';
$sFilename2	= $sDirname2 . '/file.txt';
$sFilename3	= $sDirname3 . '/file.txt';
$aData 		= array ('filename' => $sDirname);

!file_exists($sDirname) or burn('UnexpectedValueException',
	_WT('The test could not be run because the test directory already exist. You should run "make fclean".'));

@mkdir($sDirname3, 0755, true) or burn('UnexpectedValueException',
	_WT('The test could not be run because the test directory could not be created.'));

touch($sFilename);
touch($sFilename2);
touch($sFilename3);

if (!defined('WEE_ON_WINDOWS')) {
	// These tests are not compatible with Windows

	chmod($sDirname, 0400);
	$o = new weeFsDirectoryModel($aData);

	try {
		$o->deleteContents();
		$this->fail(_WT('weeFsDirectoryModel::deleteContents should trigger an error when trying to remove the contents of a directory without appropriate privileges.'));
	} catch (ErrorException $e) {}

	try {
		$o->delete();
		$this->fail(_WT('weeFsDirectoryModel::delete should trigger an error when trying to remove a directory without appropriate privileges.'));
	} catch (ErrorException $e) {}

	chmod($sDirname, 0755);
}

$o = new weeFsDirectoryModel($aData);

try {
	$o->deleteContents();
} catch (ErrorException $e) {
	$this->fail(_WT('weeFsDirectoryModel::deleteContents should not trigger an error when removing the contents of a directory with appropriate privileges.'));
}

$this->isTrue(file_exists($sDirname),
	_WT('weeFsDirectoryModel::deleteContents should not remove the given directory itself when its second argument is true.'));
// It should only contain . and .. now
$this->isEqual(2, iterator_count(new DirectoryIterator($sDirname)),
	_WT('rmdir_recursive should empty the directory that was passed to it.'));

touch($sFilename);

try {
	$o->delete();
} catch (ErrorException $e) {
	$this->fail(_WT('weeFsDirectoryModel::delete should not trigger an error when removing a directory with appropriate privileges.'));
}

$this->isFalse(file_exists($sDirname),
	_WT('weeFsDirectoryModel::delete should remove the given directory itself.'));
