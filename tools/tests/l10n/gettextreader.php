<?php

$sFilename		= ROOT_PATH . 'app/tmp/gettextreader_badfile.txt';
$sPoFilename	= ROOT_PATH . 'app/tmp/messages.po';
$sMoFilename	= ROOT_PATH . 'app/tmp/messages.mo';
$sPoContents	= '# default domain "messages.mo"
msgid  "message : ad vitam aeternam"
msgstr "translation : forever"';

touch($sFilename);
chmod($sFilename, 0644);

$iWrote = file_put_contents($sFilename, 'word');
$iWrote === false and burn('UnexpectedValueException', sprintf(_('Cannot write the file %s.'), $sFilename));

try {
	$o = new weeGetTextReader($sFilename);
	$this->fail(sprintf(_('weeGetTextReader should throw an UnexpectedValueException when reading %s.'), $sFilename));
} catch (UnexpectedValueException $e) {}

touch($sPoFilename);
chmod($sPoFilename, 0655);

$iWrote = file_put_contents($sPoFilename, $sPoContents);
$iWrote === false and burn('UnexpectedValueException', sprintf(_('Cannot write the file %s.'), $sPoFilename));

exec(sprintf('msgfmt -o %s %s', $sMoFilename, $sPoFilename));
if (!is_file($sMoFilename))
	return $this->skip();

try {
	$o = new weeGetTextReader($sMoFilename);
} catch (UnexpectedValueException $e) {
	$this->fail(sprintf(_('weeGetTextReader should not throw an UnexpectedValueException when trying to read the file %s.'), $sMoFilename));
}

$aExpectedResult = array ('message : ad vitam aeternam' => 'translation : forever');
$aResult = array();

try {
	$o = new weeGetTextReader($sMoFilename);
	$aResult = $o->getStrings();
	$this->isEqual($aExpectedResult, $aResult,
		sprintf(_('weeGetTextReader::getStrings failed, expected result : "%s=>%s", got "%s=>%s" instead.'),
		key($aExpectedResult), $aExpectedResult[key($aExpectedResult)], key($aResult), $aResult[key($aResult)]));
} catch (UnexpectedValueException $e) {
	$this->fail(sprintf(_('weeGetTextReader should not throw an UnexpectedValueException when trying to read the file %s.'), $sMoFilename));
}
