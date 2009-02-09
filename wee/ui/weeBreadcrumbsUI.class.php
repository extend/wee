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
	Breadcrumbs UI frame.

	@see http://developer.yahoo.com/ypatterns/pattern.php?pattern=breadcrumbs
*/

class weeBreadcrumbsUI extends weeUI
{
	/**
		Name of the template for the frame.
	*/

	protected $sBaseTemplate = 'breadcrumbs';

	/**
		Breadcrumbs path as an array of 'URI' => 'label'.
	*/

	protected $aPath;

	/**
		Sets a default path based on the request received if none were provided,
		and then sends the path to the template.

		@param $aEvent Event information.
	*/

	protected function defaultEvent($aEvent)
	{
		if (empty($this->aPath)) {
			$sPath = substr(weeApplication::getPathInfo(), 1);

			if (empty($sPath))
				$this->setPath(array($aEvent['frame'] => $aEvent['frame']));
			else
				$this->setPath($sPath);
		}

		$this->set('path', $this->aPath);
	}

	/**
		Sets the path rendered by the breadcrumbs UI component.

		The path can either be an array of 'URI' => 'label' pairs,
		or a path string like 'this/is/a/breadcrumbs/path'.

		@param Path to be rendered.
	*/

	public function setPath($mPath)
	{
		if (is_array($mPath))
			$this->aPath = $mPath;
		else {
			$this->aPath = array();

			$sFullPath = null;
			$aItems = explode('/', $mPath);

			foreach ($aItems as $sItem) {
				$sFullPath .= $sItem . '/';
				$this->aPath[substr($sFullPath, 0, -1)] = $sItem;
			}
		}
	}
}
