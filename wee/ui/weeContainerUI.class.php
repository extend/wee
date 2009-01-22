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
		@param $mFrame Frame to be added to the container. Can either be a class name or a weeFrame object.
	*/

	public function addFrame($sName, $mFrame)
	{
		empty($sName) and burn('InvalidArgumentException', _WT('$sName must not be empty.'));
		empty($this->aFrames[$sName]) or burn('IllegalStateException',
			sprintf(_WT('The frame named %s was already added to the container.'), $sName));

		if (!is_object($mFrame)) {
			@is_subclass_of($mFrame, 'weeFrame') or burn('UnexpectedValueException',
				sprintf(_WT('The frame %s does not exist.'), $mFrame));

			$mFrame = new $mFrame;
		}

		$mFrame->setContainer($this);
		$mFrame->setId($this->getChildIdPrefix() . $sName);
		$this->aFrames[$this->getChildIdPrefix() . $sName] = $mFrame;
	}

	/**
		TODO
	*/

	public function child($sName)
	{
		return $this->aFrames[$sName];
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
		TODO
	*/

	protected function getChildIdPrefix()
	{
		return empty($this->sId) ? '' : $this->sId . '-';
	}
}
