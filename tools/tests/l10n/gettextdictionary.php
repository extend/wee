<?php

$sPoFilename	= ROOT_PATH . 'app/tmp/messages.po';
$sMoFilename	= ROOT_PATH . 'app/tmp/messages.mo';
$aHeaders 		= array('Content-Type' => 'text/plain; charset=UTF-8', 'Plural-Forms' => 'nplurals=2; plural=(n>1);');
$aTranslation	= array('Wrong password' => 'Mot de passe incorrect');

$sPoContents	= '#default domain "messages.mo"
msgid ""
msgstr ""
"Content-Type: text/plain; charset=UTF-8\n"
"Plural-Forms: nplurals=2; plural=(n>1);\n"

msgid "Wrong password"
msgstr "Mot de passe incorrect"

msgid "Wrong number"
msgid_plural "Wrong numbers"
msgstr[0] "Mauvais chiffre"
msgstr[1] "Mauvais chiffres"';

$iWrote = file_put_contents($sPoFilename, $sPoContents);
$iWrote === false and burn('UnexpectedValueException', sprintf(_WT('Cannot write the file %s.'), $sPoFilename));

exec(sprintf('msgfmt -o %s %s', $sMoFilename, $sPoFilename));
if (!is_file($sMoFilename))
	$this->skip();

$o = new weeGetTextDictionary($sMoFilename);

// weeGetTextDictionary::getHeaders

$this->isEqual($aHeaders, $o->getHeaders(),
	_WT('weeGetTextDictionary::getHeaders does not return the expected headers.'));

// weeGetTextDictionary::getCharset

$this->isEqual('UTF-8', $o->getCharset(),
	_WT('weeGetTextDictionary::getCharset does not return the expected charset.'));

// weeGetTextDictionary::getTranslation

$this->isEqual('Mot de passe incorrect', $o->getTranslation('Wrong password'),
	_WT('weeGetTextDictionary::getTranslation does not return the expected translation.'));

$this->isEqual('foobar', $o->getTranslation('foobar'),
	_WT('weeGetTextDictionary::getTranslation does not return the expected native sentence when there is no translation available.'));

// weeGetTextDictionary::getPluralTranslation

$this->isEqual('Mauvais chiffres', $o->getPluralTranslation('Wrong number', 'Wrong numbers', 0),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected translation when n is %d.'), 0));

$this->isEqual('Mauvais chiffre', $o->getPluralTranslation('Wrong number', 'Wrong numbers', 1),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected translation when n is %d.'), 1));

$this->isEqual('Mauvais chiffres', $o->getPluralTranslation('Wrong number', 'Wrong numbers', 2),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected plural translation when n is %d.'), 2));

$this->isEqual('Mauvais chiffres', $o->getPluralTranslation('Wrong number', 'Wrong numbers', 10),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected plural translation when n is %d.'), 10));

$this->isEqual('pouet', $o->getPluralTranslation('pouet', 'Wrong numbers', 0),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected native sentence when n is %d and there is no translation available.'), 0));

$this->isEqual('pouet', $o->getPluralTranslation('pouet', 'Wrong numbers', 1),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected native sentence when n is %d and there is no translation available.'), 1));

$this->isEqual('Wrong numbers', $o->getPluralTranslation('pouet', 'Wrong numbers', 2),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected plural native sentence when n is %d and there is no translation available.'), 2));

$this->isEqual('Wrong numbers', $o->getPluralTranslation('pouet', 'Wrong numbers', 10),
	sprintf(_WT('weeGetTextDictionary::getTranslation does not return the expected plural native sentence when n is %d and there is no translation available.'), 2));
