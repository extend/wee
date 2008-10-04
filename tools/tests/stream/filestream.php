<?

$sFilenameNotExist	= 'file_which_does_not_exist.txt';
$sFilenameExist		= ROOT_PATH . 'tools/tests/stream/file.txt';

touch($sFilenameExist);
chmod($sFilenameExist, 0644);

$iWrote = file_put_contents($sFilenameExist, 'some words');
$iWrote === false and burn('UnexpectedValueException', sprintf(_('Cannot write the file %s.'), $sFilenameExist));

try {
	$oFileStream = new weeFileStream($sFilenameNotExist);
	$this->fail(sprintf(_('weeFileStream should throw a FileNotFoundException when trying to access %s.'), $sFilenameNotExist));
} catch (FileNotFoundException $e) {}

// The file exists but has not reading permission
chmod($sFilenameExist, 0000);
try {
	$oFileStream = new weeFileStream($sFilenameExist);
	$this->fail(sprintf(_('weeFileStream should throw a NotPermittedException when trying to access %s'), $sFilenameExist));
} catch (NotPermittedException $e) {}

chmod($sFilenameExist, 0644);
try {
	$oFileStream = new weeFileStream($sFilenameExist);

	$oFileStream->read(filesize($sFilenameExist) + 32);
	$this->fail(sprintf(_('read should throw an EndOfFileException when trying to read more than the size of %s.'), $sFilenameExist));
} catch (EndOfFileException $e) {}

// Seeking past EOF is not considered an error.
try {
	$oFileStream = new weeFileStream($sFilenameExist);

	$oFileStream->seek(-1);
	$this->fail(sprintf(_('seek should throw an EndOfFileException when trying to seek before the beginning of the file %s.'), $sFilenameExist));
} catch (EndOfFileException $e) {}

try {
	$oFileStream = new weeFileStream($sFilenameExist);

	$oFileStream->seek(5);
	$s = $oFileStream->read(5);
	$this->isEqual($s, 'words',
		sprintf(_("read should return the word 'words', got '%s' instead."), $s));
} catch (EndOfFileException $e) {
	$this->fail(sprintf(_('read should not throw an EndOfFileException when trying to seek/read the file %s.'), $sFilenameExist));
}

unlink($sFilenameExist);
