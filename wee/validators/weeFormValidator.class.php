<?php

/*
	Web:Extend
	Copyright (c) 2006, 2008 Dev:Extend

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

abstract class weeFormValidator extends weeValidator
{
	/**
		The data of the form.
	*/

	protected $aData;

	/**
		The widget to validate.
	*/

	protected $oWidget;

	/**
		Validates the given input.

		@throw	IllegalStateException	The validator is not attached to a form widget.
	*/

	public function validate()
	{
		$this->oWidget !== null
			or burn('IllegalStateException',
				_WT('The validator is not attached to a form widget.'));

		return parent::validate();
	}

	/**
		Sets the widget and complete data passed to the weeForm object.
		Usually either $_POST or $_GET.

		@param	$oWidget				The widget to validate.
		@param	$aData					The data to check, if applicable.
		@throw	IllegalStateException	The validator has already been attached to a form widget.
		@todo							Check that the given SimpleXMLElement is a 'widget' element.
	*/

	public function setFormData(SimpleXMLElement $oWidget, array $aData)
	{
		$this->oWidget === null
			or burn('IllegalStateException',
				_WT('The validator has already been attached to a form widget.'));

		$this->aData	= $aData;
		$this->oWidget	= $oWidget;
	}
}
