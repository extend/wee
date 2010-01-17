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
		Bufferize the output. Enable GZIP compression on demand if available.

		@param $bCompressOutput Whether to compress the output before sending it to the browser (if available).
	*/

	public static function bufferize($bCompressOutput = true)
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
}
