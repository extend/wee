<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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

fire(!defined('WEE_ON_WINDOWS'), 'BadConfigurationException',
	'This class is not available on Windows.');

/**
	Model for filesystem links.

	This class adds the following key:
	- linkto	The filename of the linked file.
*/

class weeFsLinkModel extends weeFsModel
{
	/**
		Set of filesystem objects.

		You must modify it if you use getTarget and wrote
		a child class to weeFsSet.
	*/

	protected $sSet = 'weeFsSet';

	/**
		Creates a new instance of this model and fetch related data
		using the 'filename' key from the array parameter.

		This function also retrieve the linked file filename
		and store it in the 'linkto' key.

		@param $aData Data to be set at initialization.
	*/

	public function __construct($aData = array())
	{
		parent::__construct($aData);
		$this->aData['linkto'] = readlink($aData['filename']);
	}

	/**
		Returns the model for the linked file object.

		@return weeFsModel The model for the linked file object.
	*/

	public function getTarget()
	{
		$o = new $this->sSet;
		return $o->fetch($this->aData['linkto']);
	}
}
