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
	Base class for data source objects.
	These object are required to encode the data when needed.

	Use the attached encoder to encode it.
*/

abstract class weeDataSource
{
	/**
		The encoder used to encode the data.
	*/

	protected $oEncoder;

	/**
		Encode an array.

		@param	$aValue	The array to encode.
		@return	array	The encoded array.
		@throw	IllegalStateException This source does not have an encoder.
	*/

	protected function encodeArray($mValue)
	{
		$this->getEncoder() !== null or burn('IllegalStateException',
			_WT('This data source does not have an encoder.'));

		foreach ($a as &$mValue)
			if ($mValue instanceof self)
				$mValue->encodeData($this->oEncoder);
			elseif (is_object($mValue))
				continue;
			elseif (is_array($mValue))
				$mValue = $this->encodeArray($mValue);
			else
				$mValue = $this->getEncoder()->encode($mValue);
    
		return $a;
	}

	/**
		Tells the object to automatically encode the data before returning it.

		@return $this
	*/

	public function encodeData(weeEncoder $oEncoder)
	{
		$this->oEncoder = $oEncoder;
		return $this;
	}

	/**
		Return the encoder used by this data source.

		@return weeEncoder The encoder used by this data source.
	*/

	public function getEncoder()
	{
		return $this->oEncoder;
	}
}
