<?php

$sFilenameNotExist	= 'file_which_does_not_exist.txt';
$sFilenameExist		= ROOT_PATH . 'app/tmp/stream.txt';

touch($sFilenameExist);
chmod($sFilenameExist, 0644);

$iWrote = file_put_contents($sFilenameExist, 'some words');
$iWrote === false and burn('UnexpectedValueException', sprintf(_WT('Cannot write the file %s.'), $sFilenameExist));

try {
	$oFileStream = new weeFileStream($sFilenameNotExist);
	$this->fail(sprintf(_WT('weeFileStream should throw a FileNotFoundException when trying to access %s.'), $sFilenameNotExist));
} catch (FileNotFoundException $e) {}

if (!defined('WEE_ON_WINDOWS')) {
	// The file exists but has not reading permission
	// (this test is not compatible with Windows)

	chmod($sFilenameExist, 0000);
	try {
		$oFileStream = new weeFileStream($sFilenameExist);
		$this->fail(sprintf(_WT('weeFileStream should throw a NotPermittedException when trying to access %s.'), $sFilenameExist));
	} catch (NotPermittedException $e) {}
	chmod($sFilenameExist, 0644);
}

try {
	$oFileStream = new weeFileStream($sFilenameExist);

	$oFileStream->read(filesize($sFilenameExist) + 32);
	$this->fail(sprintf(_WT('read should throw an EndOfFileException when trying to read more than the size of %s.'), $sFilenameExist));
} catch (EndOfFileException $e) {}

// Seeking past EOF is not considered an error.
try {
	$oFileStream = new weeFileStream($sFilenameExist);

	$oFileStream->seek(-1);
	$this->fail(sprintf(_WT('seek should throw an EndOfFileException when trying to seek before the beginning of the file %s.'), $sFilenameExist));
} catch (EndOfFileException $e) {}

try {
	$oFileStream = new weeFileStream($sFilenameExist);

	$oFileStream->seek(5);
	$this->isEqual('words', $oFileStream->read(5),
		_WT('weeFileStream failed to read 5 bytes from the file.'));
} catch (EndOfFileException $e) {
	$this->fail(sprintf(_WT('read should not throw an EndOfFileException when trying to seek/read the file %s.'), $sFilenameExist));
}
