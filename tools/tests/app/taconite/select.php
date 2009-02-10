<?php

class weeTaconite_testSelect extends weeTaconite {
	public function select($sSelect, DOMDocument $oDocument, DOMXPath $oXPath) {
		return parent::select($sSelect, $oDocument, $oXPath);
	}
}

$o		= new weeTaconite_testSelect;
$oDoc	= new DOMDocument;

$oDoc->loadXML('
	<root>
		<tag id="foobar"/>
		<answer value="42"/>
		<ul><li/><li/></ul>
	</root>
');

$oXPath = new DOMXPath($oDoc);

$oList = $o->select('#foobar', $oDoc, $oXPath);

$this->isEqual(1, $oList->length,
	_WT('weeTaconite::select does not return the correct number of elements for the id selection.'));

$this->isEqual('tag', $oList->item(0)->tagName,
	_WT('weeTaconite::select does not return the correct element for the id selection.'));

$oList = $o->select('//ul/li', $oDoc, $oXPath);

$this->isEqual(2, $oList->length,
	_WT('weeTaconite::select does not return the correct number of elements for the xpath selection.'));

$aTagNames = array();
foreach ($oList as $oElement)
	$aTagNames[] = $oElement->tagName;

$this->isEqual(array('li', 'li'), $aTagNames,
	_WT('weeTaconite::select does not return the correct elements for the xpath selection.'));

$oList = $o->select('answer', $oDoc, $oXPath);

$this->isEqual(1, $oList->length,
	_WT('weeTaconite::select does not return the correct number of elements for the tag selection.'));

$oAnswer = $oList->item(0);

$this->isTrue($oAnswer->hasAttribute('value'),
	_WT('weeTaconite::select should return an element with an `answer` attribute for the tag selection.'));

$this->isEqual(42, $oList->item(0)->getAttributeNode('value')->value,
	_WT('weeTaconite_testSelect::select should return an element which `answer` attribute value is "42" for the tag selection.'));
