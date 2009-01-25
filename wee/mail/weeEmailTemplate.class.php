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
	Email templates handling.
	Load, configure and display templates, while also retrieving headers
	defined in the template.
*/

class weeEmailTemplate extends weeTemplate
{
	/**
		Email headers.
		The headers are available from reading after calling the
		function toString that renders the email template.

		The possible headers include all the properties defined by
		weeSendMail. This means that to fully define the From header,
		for example, you must set From and FromName.
	*/

	public $aHeaders = array();

	/**
		Return the template as a string after extracting the headers
		from the template and making them accessible in $aHeaders.

		@return string The template.
	*/

	public function toString()
	{
		// Switch to text output, get email string, switch back

		$oOutput = weeOutput::select(new weeTextOutput);
		$sEmail = parent::toString();
		weeOutput::select($oOutput);

		// Retrieve email headers

		while (true) {
			$i = strpos($sEmail, "\n");
			$sLine = substr($sEmail, 0, $i);
			$sEmail = substr($sEmail, $i + 1);

			if (empty($sLine))
				break;

			// Malformed email header
			strpos($sLine, ': ') === false and burn('UnexpectedValueException',
				_WT('Malformed email header. Please make sure name and value are separated by ": ".' .
				" If you didn't specify any header in the template file please check that you added an empty line at the top."));

			$a = explode(': ', $sLine);
			$this->aHeaders[$a[0]] = $a[1];
		};

		return $sEmail;
	}
}
