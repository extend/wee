<?php

/*
	Dev:Extend Web Library
	Copyright (c) 2006 Dev:Extend

	This software is licensed under the Dev:Extend Public License. You can use,
	copy, modify and/or distribute the software under the terms of this License.

	This software is distributed WITHOUT ANY WARRANTIES, including, but not
	limited to, the implied warranties of MERCHANTABILITY and FITNESS FOR A
	PARTICULAR PURPOSE. See the Dev:Extend Public License for more details.

	You should have received a copy of the Dev:Extend Public License along with
	this software; if not, you can download it at the following url:
	http://dev-extend.eu/license/.
*/

if (!defined('ALLOW_INCLUSION')) die;

/**
	Class for Atom feeds generation.
*/

class weeAtomFeed extends weeFeed
{
	/**
		Convenience function for creating atom feeds in one line.

		@return weeAtomFeed A new weeAtomFeed object.
	*/

	public static function create()
	{
		return new self;
	}

	/**
		Converts an element to its XML equivalent.
		Called when generating the feed's XML.

		@param	$sName	The name of the element.
		@param	$sValue	The value of the element.
		@return	string	The XML element created according to the given name.

		TODO:there must be a better way for this
	*/

	protected function writeElement(XMLWriter $oXMLWriter, $sName, $mValue)
	{
		switch ($sName)
		{
			case 'link':
				$sName	= 'alternate';
				$mValue	= $this->encodeIRI($mValue);

			case 'self':
				$oXMLWriter->startElement('link');
				$oXMLWriter->writeAttribute('rel', $sName);
				$oXMLWriter->writeAttribute('href', $mValue);
				break;

			case 'category':
				$oXMLWriter->startElement('category');
				$oXMLWriter->writeAttribute('term', $mValue);
				break;

			case 'published':
			case 'updated':
				if (ctype_digit($mValue))
					$mValue = @date('c', $mValue);

				$oXMLWriter->startElement($sName);
				$oXMLWriter->text($mValue);
				break;

			case 'summary':
				$oXMLWriter->startElement($sName);
				$oXMLWriter->writeAttribute('type', 'xhtml');	// TODO: better
				$oXMLWriter->writeRaw($mValue);
				break;

			default:
				$oXMLWriter->startElement($sName);

				if (is_array($mValue))
				{
					foreach ($mValue as $sSubName => $sSubValue)
						$oXMLWriter->writeElement($sSubName, $sSubValue);
				}
				else
					$oXMLWriter->text($mValue);
		}

		$oXMLWriter->endElement();
	}

	/**
		Encode the IRI address.

		@param	$sIRI	Unencoded IRI address.
		@return	string	Encoded IRI address.
	*/

	public function encodeIRI($sIRI)
	{
		$sIRI			= substr($sIRI, 7); // Remove http://
		$sEncodedIRI	= urlencode(substr($sIRI, strpos($sIRI, '/') + 1));

		return 'http://' . substr($sIRI, 0, strpos($sIRI, '/') + 1) . $sEncodedIRI;
	}

	/**
		Returns the feed in XML format.

		@return string The XML for this feed.
	*/

	public function toString()
	{
		$oXMLWriter = new XMLWriter;
		$oXMLWriter->openMemory();
		$oXMLWriter->startDocument('1.0', 'utf-8');
		$oXMLWriter->startElement('feed');
		$oXMLWriter->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');

		foreach ($this->aFeed as $sName => $mValue)
			$this->writeElement($oXMLWriter, $sName, $mValue);

		foreach ($this->aEntries as $aEntry)
		{
			$oXMLWriter->startElement('entry');

			foreach ($aEntry as $sName => $mValue)
				$this->writeElement($oXMLWriter, $sName, $mValue);

			$oXMLWriter->endElement();
		}

		$oXMLWriter->endElement();
		return $oXMLWriter->flush();
	}
}

?>
