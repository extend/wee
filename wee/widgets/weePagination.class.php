<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
	Base class for pagination widgets.
*/

abstract class weePagination implements Printable
{
	/**
		Display page containing this item number.
		This number is always changed internally to the first item of the page.
	*/

	protected $iFrom;

	/**
		Number of items to display per page.
	*/

	protected $iPerPage;

	/**
		
	*/

	protected $iTotal;

	/**
		Base URI for the pagination destination links.
	*/

	protected $sURI;

	/**
		Initialize the pagination widget.

		Will work whatever $iPageWithItem is.
		If too small or too big, it's rounded to a correct value.
		The value of $iFrom property is derived from $iPageWithItem.

		@param	$iTotalItems	Total number of items.
		@param	$iItemsPerPage	Number of items to display per page.
		@param	$iPageWithItem	Display page containing this item number.
	*/

	public function __construct($iTotalItems, $iItemsPerPage, $iPageWithItem = 0)
	{
		fire(!ctype_digit((string)$iTotalItems) || $iTotalItems <= 0, 'InvalidArgumentException');
		fire(!ctype_digit((string)$iItemsPerPage) || $iItemsPerPage <= 0, 'InvalidArgumentException');
		fire(!ctype_digit((string)$iPageWithItem), 'InvalidArgumentException');

		if ($iPageWithItem < 0)
			$iPageWithItem = 0;

		$this->iTotal	= $iTotalItems;
		$this->iPerPage	= $iItemsPerPage;

		if ($iPageWithItem < $iTotalItems)
			$this->iFrom = floor($iPageWithItem / $iItemsPerPage) * $iItemsPerPage;
		else
			$this->iFrom = (ceil($iTotalItems / $iItemsPerPage) - 1) * $iItemsPerPage;

		$this->setURI($_SERVER['PHP_SELF']);
	}

	/**
		Return the first item that will be displayed.
		Useful for SQL queries.

		@return int The first item number displayed.
	*/

	public function from()
	{
		return $this->iFrom;
	}

	/**
		Calculates the first and last pages displayed by the pagination component.

		@param $iCurrent	The current page.
		@param $iNbLinks	The number of page links.
		@param $iFrom		[OUT] The first page in the links.
		@param $iTo			[OUT] The last page in the links. (+1)
	*/

	protected function getPagesFromTo($iCurrent, $iNbLinks, &$iFrom, &$iTo)
	{
		$iFrom	= 0;
		$iTo	= ceil($this->iTotal / $this->iPerPage);

		$iMiddle	= ceil($iNbLinks / 2) - 1;
		if ($iCurrent > $iMiddle)
			$iFrom	= $iCurrent - $iMiddle;

		if ($iTo > $iFrom + $iNbLinks)
			$iTo	= $iFrom + $iNbLinks;
		elseif ($iFrom > $iTo - $iNbLinks)
		{
			$iFrom	= $iTo - $iNbLinks;
			if ($iFrom < 0)
				$iFrom = 0;
		}
	}

	/**
		Return the XHTML for a page link.
		Note that no links is created for the current page.

		@param	$iPage		The page number.
		@param	$bCurrent	Wheter this is the current page.
		@return	string		The XHTML for the page link.
	*/

	protected function getPageLink($iPage, $bCurrent)
	{
		if ($bCurrent)
			return ($iPage + 1);
		return '<a href="' . $this->sURI . 'from=' . ($iPage * $this->iPerPage) . '">' . ($iPage + 1) . '</a>';
	}

	/**
		Create and return the XHTML for all page links.

		@param	$iCurrent	The displayed page number.
		@param	$iFrom		The first page in the links.
		@param	$iTo		The last page in the links. (+1)
		@return	string		The XHTML for all page links.
	*/

	protected function getPagesLinks($iCurrent, $iFrom, $iTo)
	{
		$s = null;

		for ($i = $iFrom; $i < $iTo; $i++)
			$s .= $this->getPageLink($i, $i == $iCurrent) . ' ';

		return $s;
	}

	/**
		Returns the number of items per page.

		@return int The number of items per page.
	*/

	public function numItems()
	{
		return $this->iPerPage;
	}

	/**
		Set the base URI for page links.

		@param $sURI The base URI.
	*/

	public function setURI($sURI)
	{
		//TODO:check URI

		$sURI .= (strpos($sURI, '?') === false) ? '?' : '&';
		$this->sURI = weeOutput::encodeValue($sURI);
	}

	/**
		Return the last item that will be displayed.
		Useful for SQL queries.

		@return int The last item number displayed.
	*/

	public function to()
	{
		$i = $this->iFrom + $this->iPerPage - 1;
		if ($i >= $this->iTotal)
			return $this->iTotal - 1;
		return $i;
	}

	/**
		Returns the widget XHTML code.

		@return string XHTML for this widget.
	*/

	abstract public function toString();
}

?>
