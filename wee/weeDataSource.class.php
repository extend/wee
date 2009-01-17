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
	Base class for data source objects.
	These object are required to encode the data when needed.

	Use weeOutput::encodeValue or weeOutput::encodeArray to encode it.
*/

abstract class weeDataSource
{
	/**
		Whether to automatically encode the data before returning it.
	*/

	protected $bMustEncodeData = false;

	/**
		Tells the object to automatically encode the data before returning it.

		@return $this
	*/

	public function encodeData()
	{
		$this->bMustEncodeData = true;
		return $this;
	}
}
