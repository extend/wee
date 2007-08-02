<?php

if (!defined('ALLOW_INCLUSION')) die;

/**
	Performs operation on an XML document

	@see http://www.malsup.com/jquery/taconite/
	@warning Incomplete.
*/

class weeTaconite implements Printable
{
	/**
		The XML taconite document.
	*/

	protected $sXML;

	/**
		Add a tag to the taconite document.

		@param	$sTagName	The tag name.
		@param	$sSelect	The selector.
		@param	$sContents	The content of the tag.
		@return	$this		For chained methods.
	*/

	public function addTag($sTagName, $sSelect, $sContents = null)
	{
		//TODO:filter $sTagName, $sSelect
		//TODO:check if $sContents is valid XML?

		if ($sContents instanceof weePrintable)
			$sContents = $sContents->toString();

		$this->sXML .= '<' . $sTagName . ' select="' . htmlspecialchars($sSelect) . '"';
		if ($sContents !== null)
			$this->sXML .= '>' . $sContents . '</' . $sTagName . '>';
		else
			$this->sXML .= '/>';

		return $this;
	}

	/**
		Perform an "after" operation.
		
		@param	$oAction	The taconite action.
		@param	$oElement	The document element.
	*/

	protected function applyTagAfter(DOMNode $oAction, DOMNode $oElement)
	{
		if ($oElement->nextSibling)
		{
			$oReference = $oElement->nextSibling;

			foreach ($oAction->childNodes as $oChild)
			{
				$oChild = $oElement->ownerDocument->importNode($oChild, true);
				$oElement->parentNode->insertBefore($oChild, $oReference);
			}
		}
		else
			foreach ($oAction->childNodes as $oChild)
			{
				$oChild = $oElement->ownerDocument->importNode($oChild, true);
				$oElement->parentNode->appendChild($oChild);
			}
	}

	/**
		Perform an "append" operation.
		
		@param	$oAction	The taconite action.
		@param	$oElement	The document element.
	*/

	protected function applyTagAppend(DOMNode $oAction, DOMNode $oElement)
	{
		foreach($oAction->childNodes as $oChild)
		{
			$oChild = $oElement->ownerDocument->importNode($oChild, true);
			$oElement->appendChild($oChild);
		}
	}

	/**
		Perform a "before" operation.
		
		@param	$oAction	The taconite action.
		@param	$oElement	The document element.
	*/

	protected function applyTagBefore(DOMNode $oAction, DOMNode $oElement)
	{
		foreach ($oAction->childNodes as $oChild)
		{
			$oChild = $oElement->ownerDocument->importNode($oChild, true);
			$oElement->parentNode->insertBefore($oChild, $oElement);
		}
	}

	/**
		Perform a "prepend" operation.
		
		@param	$oAction	The taconite action.
		@param	$oElement	The document element.
	*/

	protected function applyTagPrepend(DOMNode $oAction, DOMNode $oElement)
	{
		if ($oElement->firstChild)
		{
			$oReference = $oElement->firstChild;

			foreach($oAction->childNodes as $oChild)
			{
				$oChild = $oElement->ownerDocument->importNode($oChild, true);
				$oElement->insertBefore($oChild, $oReference);
			}
		}
		else
			foreach($oAction->childNodes as $oChild)
			{
				$oChild = $oElement->ownerDocument->importNode($oChild, true);
				$oElement->appendChild($oChild);
			}
	}

	/**
		Perform a "remove" operation.
		
		@param	$oAction	The taconite action.
		@param	$oElement	The document element.
	*/

	protected function applyTagRemove(DOMNode $oAction, DOMNode $oElement)
	{
		$oElement->parentNode->removeChild($oElement);
	}

	/**
		Perform a "replace" operation.
		
		@param	$oAction	The taconite action.
		@param	$oElement	The document element.
	*/

	protected function applyTagReplace(DOMNode $oAction, DOMNode $oElement)
	{
		$this->applyTagBefore($oAction, $oElement);
		$this->applyTagRemove($oAction, $oElement);
	}

	/**
		Apply taconite operations against an XML document.

		Note:	Only works with id attributes, XPath and XML tags!
				So if compatibility with non-ajax browsers is required,
				you should only use these!
				Your XML document has to be validated against a doctype
				to be able to use id attributes.

		@param	$sXMLDocument	The XML document.
		@return	string			The modified XML document.
	*/

	public function applyTo($sXMLDocument)
	{
		//TODO:test

		if ($sXMLDocument instanceof weePrintable)
			$sXMLDocument = $sXMLDocument->toString();

		$oDocument = new DOMDocument();
		$oDocument->validateOnParse = true;
		$b = $oDocument->loadXML($sXMLDocument);
		fire(!$b, 'BadXMLException');

		$oXML = new DOMDocument();
		$b = $oXML->loadXML($this->toString());
		fire(!$b, 'BadXMLException');

		foreach ($oXML->documentElement->childNodes as $oAction)
		{
			//TODO:Ignore some of the selects that we can't handle (yet)
			//TODO:not all actions have a select attribute

			$aElements = $this->select($oAction->getAttribute('select'), $oDocument);
			foreach ($aElements as $oElement)
			{
				$sFunc = 'applyTag' . ucwords($oAction->nodeName);
				$this->$sFunc($oAction, $oElement);
			}
		}

		return $oDocument->saveXML();
		return null;//TODO
	}

	/**
		Return a new weeTaconite object
		Is this really useful?

		@return weeTaconite a new Taconite object.
	*/

	public static function create()
	{
		return new weeTaconite();
	}

	/**
		Select elements in the XML document.

		@param	$sSelect	The selector.
		@param	$oDocument	The XML document.
		@return	array		The selected elements.
	*/

	protected function select($sSelect, DOMDocument $oDocument)
	{
		if ($sSelect[0] == '#')
		{
			$oElement = $oDocument->getElementById(substr($sSelect, 1));

			if (is_null($oElement))
				return array();
			return array($oElement);
		}
		elseif (strpos($sSelect, '/') !== false)
		{
			$oXPath = new DOMXPath($oDocument);
			return $oXPath->query($sSelect);
		}

		return $oDocument->getElementsByTagName($sSelect);
	}

	/**
		Return the XML taconite string.
		
		@return string The XML taconite string.
	*/

	public function toString()
	{
		return '<taconite>' . $this->sXML . '</taconite>';
	}
}

?>
