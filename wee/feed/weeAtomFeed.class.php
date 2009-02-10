<?php

/**
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

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
					$mValue = date('c', $mValue);

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
		Output the feed as application/atom+xml.
		This method sends the required header automatically.
	*/

	public function render()
	{
		header('Content-Type: application/atom+xml');
		echo $this->toString();
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
