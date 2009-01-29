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
	Exception thrown when an error concerning XML handling occurs.
*/

class BadXMLException extends LogicException
{
	/**
		Initialize a new BadXMLException instance.

		@param	$sMessage	The message of the exception.
		@param	$oError		The libxml error associated to the exception.
	*/

	public function __construct($sMessage, LibXmlError $oError = null)
	{
		if ($oError) {
			$sMessage .= "\n";
			$sMessage .= sprintf(
				_WT('libxml returned the following error (line %d, column %d):'),
				$oError->line,
				$oError->column
			);
			$sMessage .= "\n" . $oError->message;
		}
		parent::__construct($sMessage);
	}
}
