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

abstract class weePagination
{
	protected $sURI;

	public function __construct($iTotalItems, $iItemsPerPage, $iFromItem = 0)
	{
		fire(!ctype_digit((string)$iTotalItems) || $iTotalItems <= 0, 'InvalidArgumentException');
		fire(!ctype_digit((string)$iItemsPerPage) || $iItemsPerPage <= 0, 'InvalidArgumentException');
		fire(!ctype_digit((string)$iFromItem), 'InvalidArgumentException');

		if ($iFromItem < 0)
			$iFromItem = 0;

		$this->iTotal	= $iTotalItems;
		$this->iPerPage	= $iItemsPerPage;

		if ($iFromItem < $iTotalItems)
			$this->iFrom = floor($iFromItem / $iItemsPerPage) * $iItemsPerPage;
		else
			$this->iFrom = (ceil($iTotalItems / $iItemsPerPage) - 1) * $iItemsPerPage;

		$this->setURI($_SERVER['PHP_SELF']);
	}

	abstract public function __toString();

	public function from()
	{
		return $this->iFrom;
	}

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

	protected function getPageLink($iPage, $bCurrent)
	{
		if ($bCurrent)
			return ($iPage + 1);
		return '<a href="' . $this->sURI . 'from=' . ($iPage * $this->iPerPage) . '">' . ($iPage + 1) . '</a>';
	}

	protected function getPagesLinks($iCurrent, $iFrom, $iTo)
	{
		$s = null;

		for ($i = $iFrom; $i < $iTo; $i++)
			$s .= $this->getPageLink($i, $i == $iCurrent) . ' ';

		return $s;
	}

	public function numItems()
	{
		return $this->iPerPage;
	}

	public function setURI($sURI)
	{
		//TODO:check URI

		$sURI .= (strpos($sURI, '?') === false) ? '?' : '&';
		$this->sURI = weeOutput::encodeValue($sURI);
	}

	public function to()
	{
		$i = $this->iFrom + $this->iPerPage - 1;
		if ($i >= $this->iTotal)
			return $this->iTotal - 1;
		return $i;
	}
}

?>
