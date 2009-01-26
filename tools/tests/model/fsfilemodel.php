<?php

$sFilename 			= ROOT_PATH . 'app/tmp/fsfilemodel.txt';
$sContents 			= 'some ';
$sExpectedContents 	= 'some words';
$aData 				= array('filename' => $sFilename);

touch($sFilename);

$iSize = file_put_contents($sFilename, $sContents);
$iSize === false and burn('UnexpectedValueException',
	sprintf(_WT('The test could not be run because the test file could not be written to "%s".'), $sFilename));

chmod($sFilename, 0400);
try {
	$o = new weeFsFileModel($aData);
	$o->appendContents('contents');
	$this->fail(sprintf(_WT('weeFsFileModel::appendContents should trigger an error when the file is not writable.'), $sFilename));
} catch (ErrorException $e) {}

if (!defined('WEE_ON_WINDOWS')) {
	// This test is not compatible with Windows

	chmod($sFilename, 0200);
	try {
		$o = new weeFsFileModel($aData);
		$o->getContents();
		$this->fail(_WT('weeFsFileModel::getContents should trigger an error when the file is not readable.'));
	} catch (ErrorException $e) {}
}

chmod($sFilename, 0600);

try {
	$o = new weeFsFileModel($aData);
	$o->appendContents('words');
	$this->isEqual($sExpectedContents, $o->getContents(),
		_WT('weeFsFileModel::getContents does not return the expected file contents.'));
} catch (ErrorException $e) {
	$this->fail(_WT('weeFsFileModel::getContents should not trigger an error when the file exists and is readable.'), $sFilename);
}
