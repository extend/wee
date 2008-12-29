<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	Pagination UI frame.
*/

class weePaginationUI extends weeUI
{
	/**
		Name of the template for the frame.
	*/

	protected $sBaseTemplate = 'item';

	/**
		Default prefix for pagination templates.
	*/

	protected $sBaseTemplatePrefix = 'ui/pagination/';

	/**
		Base link for all pagination links.
	*/

	protected $sLink;

	/**
		Total number of pages.
	*/

	protected $iTotal = 0;

	/**
		Retrieve the page number from $aEvent['get']['page'],
		and use it to configure the pagination component.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		$this->iTotal < 0 and burn('IllegalStateException', _WT('The $iTotal property should not be < 0.'));

		$iPage = (int)array_value($aEvent['get'], 'page', 0);

		// TODO:burn instead
		if ($iPage < 0)
			$iPage = 0;
		elseif ($iPage > $this->iTotal)
			$iPage = $this->iTotal;

		$this->set(array(
			'current_page' => $iPage,
			'total_pages' => $this->iTotal,
			'nav_link' => $this->sLink,
		));
	}

	/**
		Define the base link used by all pagination links.

		@param $sLink Base link.
	*/

	public function setLink($sLink)
	{
		$this->sLink = $sLink;
	}

	/**
		Define the total number of pages.

		@param $iTotal Total number of pages.
	*/

	public function setTotal($iTotal)
	{
		$iTotal < 0 and burn('InvalidParameterException', _WT('The $iTotal property should not be < 0.'));

		$this->iTotal = $iTotal;
	}
}
