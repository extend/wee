<?php

/*
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
	Base UI frame.
*/

class weeUI extends weeFrame implements weeFailSafeRouting
{
	/**
		Default prefix for UI components.
	*/

	protected $sBaseTemplatePrefix = 'ui/';

	/**
		Whether the frame should render taconite in xmlhttprequest context.
	*/

	protected $bNoTaconite = false;

	/**
		ID for the frame.
	*/

	protected $sId;

	/**
		Return the taconite object for this frame.

		@param weeTaconite Taconite for this frame.
	*/

	public function getTaconite()
	{
		return $this->oTaconite;
	}

	/**
		Tells this frame to not use taconite.
	*/

	public function noTaconite()
	{
		$this->bNoTaconite = true;
	}

	/**
		Output the template, or the taconite object if it was used.
	*/

	public function render()
	{
		if ($this->sContext == 'xmlhttprequest') {
			if ($this->bNoTaconite)
				$this->oTaconite = null;
			elseif (empty($this->oTaconite))
				$this->oTaconite = new weeTaconite;
		}

		parent::render();
	}

	/**
		Set the ID for the frame.

		@param $sId ID for the frame.
	*/

	public function setId($sId)
	{
		$this->sId = $sId;
	}

	/**
		Allows you to change the template this UI component will use to render itself.

		@param $sFullPathToTemplate Full path to the template, including any prefix.
	*/

	public function setTemplate($sFullPathToTemplate)
	{
		$this->sBaseTemplate = $sFullPathToTemplate;
		$this->sBaseTemplatePrefix = null;
	}
}
