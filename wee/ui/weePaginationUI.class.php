<?php

/*
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
		Frame's parameters.
	*/

	protected $aParams = array();

	/**
		Retrieve the page number from $aEvent['get']['page'],
		and use it to configure the pagination component.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		$iTotal = (int)array_value($this->aParams, 'total', 0);
		$iTotal < 0 and burn('IllegalStateException',
			_WT('The $iTotal property should not be < 0.'));

		$iPage = (int)array_value($aEvent['get'], 'page', 0);
		($iPage < 0 || $iPage > $iTotal) and burn('OutOfRangeException',
			_WT('The page number is out of range.'));

		$this->set(array(
			'current_page'	=> $iPage,
			'total_pages'	=> $iTotal,
			'nav_link'		=> array_value($this->aParams, 'url'),
		));
	}

	/**
		Define the frame's parameters.

		Parameters can include:
			- total:	Total number of pages.
			- url:		The base URL for the navigation links.

		@param $aParams Frame's parameters.
	*/

	public function setParams($aParams)
	{
		$this->aParams = $aParams + $this->aParams;
	}
}
