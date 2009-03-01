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
	Base class for output drivers.
*/

abstract class weeOutput
{
	/**
		Instance of the current output driver.
		There can only be one at the same time.
	*/

	protected static $oInstance;

	/**
		Initialize the output driver. Start output buffering if requested.
	*/

	public function __construct($aParams = array())
	{
		if (empty(self::$oInstance))
			self::$oInstance = $this;

		if (!empty($aParams['buffer']))
			$this->bufferize(!empty($aParams['buffer.gzip']));
	}

	/**
		Bufferize the output. Enable GZIP compression on demand if available.

		@param $bCompressOutput Whether to compress the output before sending it to the browser (if available).
	*/

	public function bufferize($bCompressOutput = true)
	{
		$bGZIP = $bCompressOutput && !empty($_SERVER['HTTP_ACCEPT_ENCODING'])
			&& in_array('gzip', explode(',', str_replace(', ', ',', $_SERVER['HTTP_ACCEPT_ENCODING'])));

		if (defined('WEE_GZIP') || ini_get('output_buffering') || ini_get('zlib.output_compression') || !$bGZIP)
			ob_start();
		else {
			safe_header('Content-Encoding: gzip');
			ob_start('ob_gzhandler');

			// Flag indicating we sent a gzip header
			define('WEE_GZIP', 1);
		}
	}

	/**
		Decode a given value.

		@param	$mValue	The value to decode.
		@return	string	The decoded value.
	*/

	public abstract function decode($mValue);

	/**
		Encodes data to be displayed.

		@param	$mValue	Data to encode.
		@return	string	Data encoded.
	*/

	public abstract function encode($mValue);

	/**
		Encode an array of data to be displayed.

		Mainly used by weeTemplate to encode the data it received.
		You should not have to use this method.

		@param	$a		Data array to encode.
		@return	array	Data array encoded.
	*/

	public function encodeArray($a)
	{
		foreach ($a as $mName => $mValue) {
			if ($mValue instanceof weeDataSource)
				$a[$mName] = $mValue->encodeData();
			elseif (is_object($mValue))
				continue;
			elseif (is_array($mValue))
				$a[$mName] = $this->encodeArray($mValue);
			else
				$a[$mName] = $this->encode($mValue);
		}

		return $a;
	}

	/**
		Return the currently selected instance.
		Throw an exception if no instances are selected.

		@return weeOutput The selected output instance.
	*/

	public static function instance()
	{
		empty(self::$oInstance) and burn('IllegalStateException',
			_WT('An instance of weeOutput must be created before it can be retrieved. Please make sure that you have '
				. 'an output driver started before doing any code related to output, be it forms or templates.'));

		return self::$oInstance;
	}

	/**
		Select a new output driver and return the previous one.

		@param $oOutput New driver to be used.
		@return weeOutput The driver being replaced.
	*/

	public static function select(weeOutput $oOutput)
	{
		$oOld = weeOutput::$oInstance;
		weeOutput::$oInstance = $oOutput;
		return $oOld;
	}
}
