<?php

// after

$o = weeTaconite::create()->addTag('after', 'this', '<that/>');

$this->isEqual('<root><this/><that/></root>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `after` tags.'));

// append

$o = weeTaconite::create()->addTag('append', 'this', '<append/>');

$this->isEqual('<root><this><that/><append/></this></root>', $o->applyTo('<root><this><that/></this></root>'),
	_WT('weeTaconite does not correctly apply `append` tags.'));

// before

$o = weeTaconite::create()->addTag('before', 'this', '<that/>');

$this->isEqual('<root><that/><this/></root>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `before` tags.'));

// prepend

$o = weeTaconite::create()->addTag('prepend', 'this', '<prepend/>');

$this->isEqual('<root><this><prepend/><that/></this></root>', $o->applyTo('<root><this><that/></this></root>'),
	_WT('weeTaconite does not correctly apply `prepend` tags.'));

// remove

$o = weeTaconite::create()->addTag('remove', 'this');

$this->isEqual('<root/>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `remove` tags.'));

// replace

$o = weeTaconite::create()->addTag('replace', 'this', '<that/>');

$this->isEqual('<root><that/></root>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `replace` tags.'));

// replaceContent

$o = weeTaconite::create()->addTag('replaceContent', 'this', '<replaceContent/>');

$this->isEqual('<root><this><replaceContent/></this></root>', $o->applyTo('<root><this><that/></this></root>'),
	_WT('weeTaconite does not correctly apply `replaceContent` tags.'));

$o = weeTaconite::create()->addTag('invalid', '', '<invalidXML>');

// Bad Taconite

try {
	$o->applyTo('<root/>');
	$this->fail(_WT('weeTaconite::applyTo should throw a BadXMLException when the Taconite object is not a well-formed XML document.'));
} catch (BadXMLException $e) {}

// Bad document

try {
	weeTaconite::create()->applyTo('<root>');
	$this->fail(_WT('weeTaconite::applyTo should throw a BadXMLException when the given string is not a well-formed XML document.'));
} catch (BadXMLException $e) {}
