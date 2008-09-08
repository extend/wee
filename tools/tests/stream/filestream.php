<?

$sFileNameNotExist	= 'file_which_does_not_exist.txt';
$sFileNameExist		= ROOT_PATH . 'tools/tests/stream/file.txt';
touch($sFileNameExist);

$rHandle = fopen($sFileNameExist, 'w');
$rHandle === false and burn('UnexpectedValueException', sprintf(_('Cannot open file %s.'), $sFileNameExist));

$iWrote = fwrite($rHandle, 'some words');
$iWrote === false and burn('UnexpectedValueException', sprintf(_('Cannot write the file %s.'), $sFileNameExist));

fclose($rHandle);

try {
	$oFileStream = new weeFileStream($sFileNameNotExist);
	$this->fail(sprintf(_('weeFileStream should not throw a FileNotFoundException when trying to access %s.'), $sFileNameNotExist));
} catch (FileNotFoundException $e) {}

// The file exists but has not reading permission
chmod($sFileNameExist, 0000);
try {
	$oFileStream = new weeFileStream($sFileNameExist);
	$this->fail(sprintf(_('weeFileStream should not throw a FileNotFoundException when trying to access %s'), $sFileNameExist));
} catch (FileNotFoundException $e) {}
chmod($sFileNameExist, 0644);

try {
	$oFileStream = new weeFileStream($sFileNameExist);
	$oFileStream->read(filesize($sFileNameExist) + 32);
	$this->fail(sprintf(_('read should not throw an EndOfFileException when trying to read more than the size of %s.'), $sFileNameExist));
} catch (EndOfFileException $e) {}

// Seeking past EOF is not considered an error.
try {
	$oFileStream = new weeFileStream($sFileNameExist);
	$oFileStream->seek(-1);
	$this->fail(sprintf(_('seek should not throw an EndOfFileException when trying to seek before the beginning of the file %s.'), $sFileNameExist));
} catch (EndOfFileException $e) {}

try {
	$oFileStream = new weeFileStream($sFileNameExist);
	$oFileStream->seek(5);
	$s = $oFileStream->read(5);
	$this->isEqual($s, 'words',
		sprintf(_("read should return the word 'words', got '%s' instead."), $s));
} catch (EndOfFileException $e) {
	$this->fail(sprintf(_('read should not throw an EndOfFileException when trying to seek/read the file %s.'), $sFileNameExist));
}
