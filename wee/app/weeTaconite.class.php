<?php

/**
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

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
		Append a taconite string to this object.

		@param $mTaconite The taconite string or object to append.
	*/

	public function add($mTaconite)
	{
		if (is_object($mTaconite))
			$mTaconite = $mTaconite->getTags();

		$this->sXML .= $mTaconite;
	}

	/**
		Add a tag to the taconite document.

		@param	$sTagName	The tag name.
		@param	$sSelect	The selector.
		@param	$sContents	The content of the tag.
		@return	$this		For chained methods.
	*/

	public function addTag($sTagName, $sSelect, $sContents = null)
	{
		//TODO:filter $sTagName
		//TODO:check if $sContents is valid XML?

		if ($sContents instanceof Printable)
			$sContents = $sContents->toString();

		$this->sXML .= '<' . $sTagName . ' select="' . xmlspecialchars($sSelect) . '"';
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
		Perform a "replaceContent" operation.

		@param $oAction		The taconite action.
		@param $oElement	The document element.
	*/

	protected function applyTagReplaceContent(DOMNode $oAction, DOMNode $oElement)
	{
		foreach ($oElement->childNodes as $oChild)
			$oElement->removeChild($oChild);
		$this->applyTagAppend($oAction, $oElement);
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
		@throw	BadXMLException	The given string is not a well-formed XML document.
		@throw	BadXMLException	The string returned by weeTaconite::toString is not a well-formed XML document.
	*/

	public function applyTo($sXMLDocument)
	{
		if ($sXMLDocument instanceof Printable)
			$sXMLDocument = $sXMLDocument->toString();

		// DOMDocument triggers a warning when its argument is empty but it is not triggered by libxml itself
		// so we cannot use libxml_get_last_error in this case.

		$sXMLDocument != '' or burn('InvalidArgumentException',
			_WT('The given string must not be empty.'));

		// Calls to DOMDocument::loadXML must be silenced because it triggers a warning when
		// its argument is not a well-formed XML document.

		// We ignore libxml warnings about undeclared entities as we cannot afford to load the
		// entire XHTML entity sets. See http://www.xmlsoft.org/html/libxml-xmlerror.html
		// for the complete list of error codes.

		$oDocument = new DOMDocument;
		if (!@$oDocument->loadXML($sXMLDocument, LIBXML_NONET)) {
			$o = libxml_get_last_error();
			if ($o->code == 26 /* XML_ERR_UNDECLARED_ENTITY */)
				throw new BadXMLException(
					_WT('The given string failed to be parsed because of an undeclared entity. '
						. 'To resolve this problem, please include a document type declaration at the top of your document.'), $o);
			elseif ($o->code != 27 /* XML_WAR_UNDECLARED_ENTITY */)
				throw new BadXMLException(_WT('The given string is not a well-formed XML document.'), $o);
		}

		$oXPath = new DOMXPath($oDocument);

		$oXML = new DOMDocument;
		if (!@$oXML->loadXML('<!DOCTYPE dummy SYSTEM "">' . $this->toString(), LIBXML_NONET)) {
			$o = libxml_get_last_error();
			// No need to check for XML_ERR_UNDECLARED_ENTITY here as we include a dummy
			// document type declaration ourselves.
			if ($o->code != 27 /* XML_WAR_UNDECLARED_ENTITY */)
				throw new BadXMLException(_WT('The string returned by weeTaconite::toString is not a well-formed XML document.'), $o);
		}

		foreach ($oXML->documentElement->childNodes as $oAction)
		{
			//TODO:Ignore some of the selects that we can't handle (yet)
			//TODO:not all actions have a select attribute

			$sFunc = 'applyTag' . ucwords($oAction->nodeName);
			if (!is_callable(array($this, $sFunc)))
				continue;

			$aElements = $this->select($oAction->getAttribute('select'), $oDocument, $oXPath);
			foreach ($aElements as $oElement)
				$this->$sFunc($oAction, $oElement);
		}

		// We cannot use LIBXML_NOXMLDECL here because it's not supported by
		// DOMDocument::saveXML.

		$s = $oDocument->saveXML();
		return rtrim(substr($s, strpos($s, '?>') + 3));
	}

	/**
		Return the taconite string (excluding the <taconite/>s tags).

		@return The taconite string.
	*/

	public function getTags()
	{
		return $this->sXML;
	}

	/**
		Output the taconite as text/xml.
		This function sends the required header automatically.
	*/

	public function render()
	{
		header('Content-Type: text/xml');

		echo '<?xml version="1.0" encoding="utf-8"?>',
			'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			$this->toString();
	}

	/**
		Select elements in the XML document.

		@param	$sSelect	The selector.
		@param	$oDocument	The XML document.
		@param	$oXPath		The XPATH object for this document.
		@return	DOMNodeList	The selected elements.
	*/

	protected function select($sSelect, DOMDocument $oDocument, DOMXPath $oXPath)
	{
		if ($sSelect[0] == '#')
			return $oXPath->query('//*[@id="' . xmlspecialchars(substr($sSelect, 1)) . '"]');

		if (strpos($sSelect, '/') !== false)
			return $oXPath->query($sSelect);

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
