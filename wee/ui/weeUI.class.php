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
	Base UI frame.
*/

class weeUI extends weeFrame implements weeFailSafeRouting
{
	/**
		Default prefix for UI components.
	*/

	protected $sBaseTemplatePrefix = 'ui/';

	/**
		TODO
	*/

	protected $oContainer;

	protected $bNoTaconite = false;

	/**
		TODO
	*/

	protected $sId;

	public function getTaconite()
	{
		return $this->oTaconite;
	}

	public function noTaconite()
	{
		$this->bNoTaconite = true;
	}

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
		TODO
	*/

	public function setContainer($oContainer)
	{
		$oContainer instanceof weeContainerUI or burn('InvalidArgumentException',
			_WT('The container must be an instance of weeContainerUI.'));

		$this->oContainer = $oContainer;
	}

	/**
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
