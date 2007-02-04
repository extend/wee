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
	Interface adding methods for form-only validators.
*/

interface weeFormValidator extends weeValidator
{
	/**
		Sets data passed to the weeForm object.
		Usually either $_POST or $_GET.

		@param $aData The data to check, if applicable.
	*/

	public function setData($aData);

	/**
		Sets the widget to validate.

		@param $oWidget The widget to validate.
	*/

	public function setWidget($oWidget);
}

?>
