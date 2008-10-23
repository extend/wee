<?php

$sFilename 			= ROOT_PATH . 'app/tmp/fsfilemodel.txt';
$sContents 			= 'some ';
$sExpectedContents 	= 'some words';
$aData 				= array('filename' => $sFilename);

touch($sFilename);

$iSize = file_put_contents($sFilename, $sContents);
$iSize === false and burn('UnexpectedValueException', sprintf(_('The %s contents could not be appended'), $sFilename));

chmod($sFilename, 0400);
try {
	$o = new weeFsFileModel($aData);
	$o->appendContents('contents');
	$this->fail(sprintf(_('weeFsFileModel::appendContents should throw an UnexpectedValueException, the file %s could not be appended.'), $sFilename));
} catch (UnexpectedValueException $e) {}

if (!defined('WEE_ON_WINDOWS')) {
	// This test is not compatible with Windows

	chmod($sFilename, 0200);
	try {
		$o = new weeFsFileModel($aData);
		$o->getContents();
		$this->fail(sprintf(_('weeFsFileModel::appendContents should throw an UnexpectedValueException, the contents of %s could not be get.'), $sFilename));
	} catch (UnexpectedValueException $e) {}
}

chmod($sFilename, 0644);

try {
	$o = new weeFsFileModel($aData);
	$o->appendContents('words');
	$this->isEqual($sExpectedContents, $o->getContents(),
		_('weeFsFileModel::getContents should return the contents of the file.'));
} catch (UnexpectedValueException $e) {
	$this->fail(sprintf(_('weeFsFileModel should not throw an UnexpectedValueException, 
		the file %s could be appended and the contents could be get.'), $sFilename));
}
