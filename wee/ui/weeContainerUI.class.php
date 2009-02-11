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
	Container UI frame.

	This class is to be used as the parent class for frames that use UI components.
	It implements mechanisms to redirect events to all UI frames that can then be
	rendered directly in the template.

	@todo eventTaconite
*/

class weeContainerUI extends weeUI
{
	/**
		Name of the template for the frame.
		If not defined its value will be the name of the frame's class.
	*/

	protected $sBaseTemplate = 'container';

	/**
		List of frames this container handles.
	*/

	protected $aFrames = array();

	/**
		Add an UI frame to the container.

		Keep in mind though that if the frame doesn't implement weeFailSafeRouting,
		there might be an exception triggered when the event doesn't exist in it.

		@param $sName Identifier for the given frame.
		@param $oFrame Frame to be added to the container.
	*/

	public function addFrame($sName, $oFrame)
	{
		empty($sName) and burn('InvalidArgumentException', _WT('$sName must not be empty.'));
		empty($this->aFrames[$sName]) or burn('IllegalStateException',
			sprintf(_WT('The frame named %s was already added to the container.'), $sName));

		$oFrame->setId($this->getChildIdPrefix() . $sName);
		$this->aFrames[$this->getChildIdPrefix() . $sName] = $oFrame;
	}

	/**
		Return a child UI frame.

		@param $sName Identifier for the given frame.
		@return weeUI UI frame.
	*/

	public function child($sName)
	{
		return $this->aFrames[$this->getChildIdPrefix() . $sName];
	}

	/**
		Dispatch the event to all contained frames before sending them to the template.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		foreach ($this->aFrames as $oFrame)
			$oFrame->dispatchEvent($aEvent);

		$this->set('frames', $this->aFrames);
	}

	/**
		Return the prefix for the child frame identifiers.

		@return Prefix for child frame identifiers.
	*/

	protected function getChildIdPrefix()
	{
		return empty($this->sId) ? '' : $this->sId . '-';
	}

	/**
		Return the taconite object for this frame and all its children.

		@param weeTaconite Taconite for this frame and all its children.
	*/

	public function getTaconite()
	{
		$oTaconite = new weeTaconite;

		if (!empty($this->oTaconite))
			$oTaconite->add($this->oTaconite);

		foreach ($this->aFrames as $oFrame) {
			$oChildTaconite = $oFrame->getTaconite();

			if (empty($oChildTaconite))
				continue;

			$oTaconite->add($oChildTaconite);
		}

		return $oTaconite;
	}

	/**
		Tells the child frames to not use taconite.
		Use this when you want to return the rendered template in your taconite response.
	*/

	public function noChildTaconite()
	{
		foreach ($this->aFrames as $oFrame)
			$oFrame->noTaconite();
	}

	/**
		Tells this frame and all its children to not use taconite.
	*/

	public function noTaconite()
	{
		$this->noChildTaconite();
		parent::noTaconite();
	}

	/**
		Output the template, or the taconite object if it was used.
	*/

	public function render()
	{
		if ($this->sContext == 'xmlhttprequest' && !$this->bNoTaconite)
			$this->oTaconite = $this->getTaconite();

		parent::render();
	}
}
