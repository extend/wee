<?php

if (!defined('ALLOW_INCLUSION')) die;

/**
	@warning Not tested yet.
	@warning Incomplete.
*/

class weeTaconite implements Printable
{
	protected $sXML;

	public function addTag($sTagName, $sSelect, $sContents)
	{
		//TODO:filter $sTagName, $sSelect
		//TODO:check if $sContents is valid XML?

		if ($sContents instanceof weePrintable)
			$sContents = $sContents->toString();

		$this->sXML .= '<' . $sTagName . ' select="' . $sSelect . '">' . $sContents . '</' . $sTagName . '>';
	}

	protected function applyTagAfter($oAction, $oElement)
	{//TODO
	}

	protected function applyTagRemove($oAction, $oElement)
	{//TODO
	}

	protected function applyTagReplace($oAction, $oElement)
	{
		$this->applyTagAfter($oAction, $oElement);
		$this->applyTagRemove($oAction, $oElement);
	}

	/**
		Note:	only works with id attributes and XHTML tags!
				CSS and XPath are NOT supported!
				So if compatibility with non-ajax browsers is required,
				you should only use these!
	*/

	public function applyTo($sXHTMLDocument)
	{
		//TODO:test
		//TODO:write the applyTag* methods
/*
		if ($sXHTMLDocument instanceof weePrintable)
			$sXHTMLDocument = $sXHTMLDocument->toString();

		$oDocument = new DOMDocument();
		$b = $oDocument->loadXML($sXHTMLDocument);
		fire($b, 'BadXMLException');

		foreach ($this->children() as $oAction)
		{
			//TODO:Ignore some of the selects that we can't handle (yet)
			//TODO:not all actions have a select attribute

			$aElements = $this->select($oAction['select']);
			foreach ($aElements as $oElement)
			{
				$sFunc = 'applyTag' . $oChild->getName();
				$this->$sFunc($oAction, $oElement);
			}
		}

		return $oDocument->asXML();*/
		return null;//TODO
	}

	public static function create()
	{
		return new weeTaconite();
	}

	protected function select($sSelect)
	{//TODO
/*		if ($sSelect[0] == '#')
		{
			$oElement = $oDocument->getElementById(substr($sSelect, 1));

			if (is_null($oElement))
				return array();
			return array($oElement);
		}

		return $oDocument->getElementsByTagName($oChild['select']);*/
	}

	public function toString()
	{
		return '<root>' . $this->sXML . '</root>';
	}
}

?>
