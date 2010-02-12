<?php

$o = new weeTaconite;
$o->addTag('append', 'root', '&eacute;');

try {
	$o->applyTo('<root/>');
} catch (BadXMLException $e) {
	$this->fail(_WT('weeTaconite does not ignore undeclared entities in Taconite documents.'));
}

try {
	$o = new weeTaconite;
	$o->addTag('append', 'root', ' &eagrave;');
	$s = $o->applyTo('<!DOCTYPE dummy SYSTEM ""><root>&eacute;</root>');
} catch (BadXMLException $e) {
	echo $e . "\n";
	$this->fail(_WT('weeTaconite does not ignore undeclared entities in the document to transform.'));
}

$this->isEqual("<!DOCTYPE dummy SYSTEM \"\">\n<root>&eacute; &eagrave;</root>", $s,
	_WT('weeTaconite fails to transform documents with undeclared entities.'));
