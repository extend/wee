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
	$this->skip();

$o = new weeGetTextReader($sMoFilename);

$aExpectedResult = array('message : ad vitam aeternam' => 'translation : forever');
$this->isEqual($aExpectedResult, $o->getStrings(),
	_('weeGetTextReader::getStrings does not return the expected strings.'));
