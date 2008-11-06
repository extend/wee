<?php

weeXHTMLOutput::select();

class weeTemplate_test extends weeTemplate {
	public function __construct() {}

	public function mkLink($sLink, $aArgs = array()) {
		return parent::mkLink($sLink, $aArgs);
	}
}

$o = new weeTemplate_test;

$this->isEqual('/foo&amp;/bar?&lt;=blah&amp;answer=42', $o->mkLink('/foo&/bar', array('<' => 'blah', 'answer' => 42)),
	_WT('weeTemplate::mkLink should encode the link with the weeOutput::encodeValue method.'));

$this->isEqual('/foo/bar?a=1&amp;b=2', $o->mkLink('/foo/bar?a=1', array('b' => '2')),
	_WT('weeTemplate::mkLink should append the given parameters if the base link already contain a query string.'));

$this->isEqual('/foo/bar?space=a+b', $o->mkLink('/foo/bar', array('space' => 'a b')),
	_WT('weeTemplate::mkLink should encode any URL parameter with the urlencode function.'));

$this->isEqual('/foo/bar?entity=%26', $o->mkLink('/foo/bar', array('entity' => '&')),
	_WT('weeTemplate::mkLink should decode the values of the URL parameters with the weeOutput::decode method before encoding them with the urlencode function.'));

/*
<?php
$this->Skip();
define('TPL_PATH',	ROOT_PATH . 'app/tmp/tpl/');

define('FORM_PATH',	ROOT_PATH . 'app/tmp/form/');
define('FORM_EXT',	'.form');

class test_weeTemplate extends weeTemplate
{

	public $aData;
	public $aLinkArgs = array();

	public function template($sTemplate, array $aData = array())
	{
		return parent::template($sTemplate, $aData);
	}

	public function mkLink($sLink, $aArgs = array())
	{
		return parent::mkLink($sLink, $aArgs);
	}
}

$sFormContents 		= '<?xml version="1.0" encoding="utf-8"?>
<form>	
	<formkey>0</formkey>
	<method>post</method>
	<widgets>
		<widget type="fieldset"><widget type="textarea" required="required"><name>data_text</name><label>Pasted text :</label></widget>
		<widget type="fieldset"><class>buttonsfieldset</class><widget type="submit"/></widget></widget>
	</widgets>
</form>';
$sFormName 			= 'pastebin';
$sTemplateName		= 'pastebin';
$sTemplateContents	= '<h2>Post a new Pastebin!</h2><?php echo $pastebin->toString()?>';
$sLink 				= 'http://www.example.com/';
$aArgs 				= array('arg1' => 'val1', 'arg2' => 'val2');
$aArray				= array('k1' => 'v1', 'k2' => 'v2');
$aExpectedArray		= array('k1' => 'dynamite', 'k2' => 'v2');
//$sExpectedLink		= 'http://www.example.com/?arg1=val1&arg2=val2';
$sExpectedLink 		= 'http://www.example.com/?arg1=val1&amp;arg2=val2';

$iRet = @mkdir(TPL_PATH, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the directory %s.'), TPL_PATH));

$iRet = @mkdir(FORM_PATH, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the directory %s.'), FORM_PATH));

$iWrote = file_put_contents(FORM_PATH . $sFormName . FORM_EXT, $sFormContents);
$iWrote === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create/write %s.'), FORM_PATH . $sFormName . FORM_EXT));

$iWrote = file_put_contents(TPL_PATH . $sTemplateName . TPL_EXT, $sTemplateContents);
$iWrote === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create/write %s.'), TPL_PATH . $sTemplateName . TPL_EXT));

try {
	$o = new test_weeTemplate('bad');
	$this->fail(sprintf(_WT('weeTemplate should throw a FileNotFoundException when trying to access %s.'), $sTemplateName));
} catch (FileNotFoundException $e) {}

try {
	weeXHTMLOutput::select();

	$oForm 	= new weeForm($sFormName);
	$o	= new test_weeTemplate($sTemplateName, array('pastebin' => $oForm));

	$this->isEqual($o->aData, array('pastebin' => $oForm),
		sprintf(_WT('weeTemplate::set the expected result was not found.')));

	$o->addLinkArgs($aArgs);
	$this->isEqual($o->aLinkArgs, $aArgs, 
		sprintf(_WT('weeTemplate::addLinkArgs the expected result was not found.')));

	$sNewLink = $o->mklink($sLink, $aArgs);
	$this->isEqual($sNewLink, $sExpectedLink, 
		sprintf(_WT('weeTemplate::mklink should return "%s" got "%s" instead.'), $sExpectedLink, $sNewLink));

	$o->set($aExpectedArray);
	$this->isEqual($o->aData, $aExpectedArray + $o->aData,
			sprintf(_WT('weeTemplate::set the expected result was not found.')));

	$o->toString();
	//~ echo $o->template($sTemplateName2);

} catch (FileNotFoundException $e) {
	$this->fail(sprintf(_WT('weeTemplate should not throw a FileNotFoundException when trying to access %s.'), TPL_PATH . $sTemplateName . TPL_EXT));
}

*/