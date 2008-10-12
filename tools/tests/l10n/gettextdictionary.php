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
msgid_plural "Wrongs numbers"
msgstr[0] "Mauvais chiffre"
msgstr[1] "Mauvais chiffres"';

$iWrote = file_put_contents($sPoFilename, $sPoContents);
$iWrote === false and burn('UnexpectedValueException', sprintf(_('Cannot write the file %s.'), $sPoFilename));

exec(sprintf('msgfmt -o %s %s', $sMoFilename, $sPoFilename));
if (!is_file($sMoFilename))
	$this->skip();

try {
	$o = new weeGetTextDictionary($sMoFilename);

	$aMoHeaders = $o->getHeaders();

	$this->isEqual($aMoHeaders, $aHeaders, _('weeGetTextDictionary::getHeaders should return 
		[Content-Type] => text/plain; charset=UTF-8 
		[Plural-Forms] => nplurals=2; plural=(n>1);'));

	$sCharset = $o->getCharset();
	$this->isEqual($sCharset, 'UTF-8', sprintf(_('weeGetTextDictionary::getCharset should return UTF-8 got "%s" instead'), $sCharset));

	$sString = $o->getTranslation('Wrong password');
	$this->isEqual($sString, 'Mot de passe incorrect',
		sprintf(_('weeGetTextDictionary::getCharset should return "Mot de passe incorrect" got "%s" instead'), $sString));

} catch (UnexpectedValueException $e) {
	$this->fail(sprintf(_('weeGetTextDictionary should not throw an UnexpectedValueException when trying to read the file %s.'), $sMoFilename));
}

try {
	$o = new weeGetTextDictionary($sMoFilename);

	$sTranslation = $o->getPluralTranslation('Wrong number', 'Wrongs numbers', 0);
	$this->isEqual($sTranslation, 'Mauvais chiffres',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Mauvais chiffres" got "%s" instead'), $sTranslation));	
	$sTranslation = $o->getPluralTranslation('Wrong number', 'Wrongs numbers', 1);
	$this->isEqual($sTranslation, 'Mauvais chiffre',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Mauvais chiffre" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('Wrong number', 'Wrongs numbers', 2);
	$this->isEqual($sTranslation, 'Mauvais chiffres',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Mauvais chiffres" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('Wrong number', 'Wrongs numbers', 10);
	$this->isEqual($sTranslation, 'Mauvais chiffres',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Mauvais chiffres" got "%s" instead'), $sTranslation));

	$sTranslation = $o->getPluralTranslation('pouet', 'Wrongs numbers', 0);
	$this->isEqual($sTranslation, 'Wrongs numbers',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Wrongs numbers" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('pouet', 'Wrongs numbers', 1);
	$this->isEqual($sTranslation, 'Wrongs numbers',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Wrongs numbers" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('pouet', 'Wrongs numbers', 2);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('pouet', 'Wrongs numbers', 10);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));

	$sTranslation = $o->getPluralTranslation('Wrongs numbers', 'pouet', 0);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('Wrong number', 'pouet', 1);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('Wrong number', 'pouet', 2);
	$this->isEqual($sTranslation, 'Wrong number',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Wrong number" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('Wrong number', 'pouet', 10);
	$this->isEqual($sTranslation, 'Wrong number',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "Wrong number" got "%s" instead'), $sTranslation));

	$sTranslation = $o->getPluralTranslation('pouet', 'pouet', 0);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('pouet', 'pouet', 1);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('pouet', 'pouet', 2);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));
	$sTranslation = $o->getPluralTranslation('pouet', 'pouet', 10);
	$this->isEqual($sTranslation, 'pouet',
		sprintf(_('weeGetTextDictionary::getPluralTranslation should return "pouet" got "%s" instead'), $sTranslation));

} catch (UnexceptedValueException $e) {}
