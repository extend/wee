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
	Class for RSS feeds generation.
*/

class weeRSSFeed extends weeFeed
{
	/**
		Convenience function for creating atom feeds in one line.

		@return weeRSSFeed A new weeRSSFeed object.
	*/

	public static function create()
	{
		return new self;
	}

	/**
		Return the MIME type of the taconite object.

		@return string application/rss+xml.
	*/

	public function getMIMEType()
	{
		return 'application/rss+xml';
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
			case 'self':
				$oXMLWriter->startElement('link');
				$oXMLWriter->writeAttribute('rel', 'self');
				$oXMLWriter->writeAttribute('href', $mValue);
				break;

			case 'published':
			case 'updated':
				$sName = $sName == 'published' ? 'pubDate' : 'lastBuildDate';
				if (ctype_digit($mValue))
					$mValue = date('r', $mValue);

				$oXMLWriter->startElement($sName);
				$oXMLWriter->text($mValue);
				break;

			case 'subtitle':
				$oXMLWriter->startElement('description');
				$oXMLWriter->text($mValue);
				break;

			case 'summary':
				$oXMLWriter->startElement('description');
				$oXMLWriter->writeCData($mValue);
				break;

			case 'logo':
				$oXMLWriter->startElement('image');
				$oXMLWriter->writeElement('url', $mValue);
				$oXMLWriter->writeElement('title', $this->aFeed['title']);
				$oXMLWriter->writeElement('link', $this->aFeed['link']);
				break;

			case 'author':
				$oXMLWriter->startElement('managingEditor');
				$oXMLWriter->text($mValue['mail']);
				if (isset($mValue['name']))
					$oXMLWriter->text(' (' . $mValue['name'] . ')');
				break;

			case 'rights':
				$oXMLWriter->startElement('copyright');
				$oXMLWriter->text($mValue);
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
		Returns the feed in XML format.

		@return string The XML for this feed.
	*/

	public function toString()
	{
		$oXMLWriter = new XMLWriter;
		$oXMLWriter->openMemory();
		$oXMLWriter->startDocument('1.0', 'utf-8');
		$oXMLWriter->startElement('rss');
		$oXMLWriter->writeAttribute('version', '2.0');
		$oXMLWriter->startElement('channel');

		foreach ($this->aFeed as $sName => $mValue)
			$this->writeElement($oXMLWriter, $sName, $mValue);

		foreach ($this->aEntries as $aEntry)
		{
			$oXMLWriter->startElement('item');

			foreach ($aEntry as $sName => $mValue)
				$this->writeElement($oXMLWriter, $sName, $mValue);

			$oXMLWriter->endElement();
		}

		$oXMLWriter->endElement();
		$oXMLWriter->endElement();
		return $oXMLWriter->flush();
	}
}
