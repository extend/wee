<?php

function weeTaconite_create()
{
	return new weeTaconite;
}

// after

$o = weeTaconite_create()->addTag('after', 'this', '<that/>');

$this->isEqual('<root><this/><that/></root>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `after` tags.'));

// append

$o = weeTaconite_create()->addTag('append', 'this', '<append/>');

$this->isEqual('<root><this><that/><append/></this></root>', $o->applyTo('<root><this><that/></this></root>'),
	_WT('weeTaconite does not correctly apply `append` tags.'));

// before

$o = weeTaconite_create()->addTag('before', 'this', '<that/>');

$this->isEqual('<root><that/><this/></root>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `before` tags.'));

// prepend

$o = weeTaconite_create()->addTag('prepend', 'this', '<prepend/>');

$this->isEqual('<root><this><prepend/><that/></this></root>', $o->applyTo('<root><this><that/></this></root>'),
	_WT('weeTaconite does not correctly apply `prepend` tags.'));

// remove

$o = weeTaconite_create()->addTag('remove', 'this');

$this->isEqual('<root/>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `remove` tags.'));

// replace

$o = weeTaconite_create()->addTag('replace', 'this', '<that/>');

$this->isEqual('<root><that/></root>', $o->applyTo('<root><this/></root>'),
	_WT('weeTaconite does not correctly apply `replace` tags.'));

// replaceContent

$o = weeTaconite_create()->addTag('replaceContent', 'this', '<replaceContent/>');

$this->isEqual('<root><this><replaceContent/></this></root>', $o->applyTo('<root><this><that/></this></root>'),
	_WT('weeTaconite does not correctly apply `replaceContent` tags.'));

$o = weeTaconite_create()->addTag('invalid', '', '<invalidXML>');

// Bad Taconite

try {
	$o->applyTo('<root/>');
	$this->fail(_WT('weeTaconite::applyTo should throw a BadXMLException when the Taconite object is not a well-formed XML document.'));
} catch (BadXMLException $e) {}

// Bad document

try {
	weeTaconite_create()->applyTo('<root>');
	$this->fail(_WT('weeTaconite::applyTo should throw a BadXMLException when the given string is not a well-formed XML document.'));
} catch (BadXMLException $e) {}
