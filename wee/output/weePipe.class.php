<?php

/**
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
	Base class for pipes.
*/

abstract class weePipe
{
	/**
		The pipe parameters.
	*/

	protected $aParams;

	/**
		Construct a new pipe.

		@param	$aParams	The pipe parameters.
		@param	$mInput		The pipe input.
	*/

	public function __construct($aParams = array())
	{
		$this->aParams = $aParams + $this->aParams;
	}

	/**
		Initialise the pipe.

		This method should start output buffering with whatever parameters
		needed by this pipe.
	*/

	abstract public function init();

	/**
		Process the pipe.

		This method should be called after the input of the pipe has been
		sent into the output buffer.
	*/

	abstract public function process();
}
