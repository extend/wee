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

require(WEE_PATH . 'vendor/phpmailer/class.phpmailer.php');

/**
	Class extending PHPMailer, used to send emails via SMTP.
*/

class weeSendMail extends PHPMailer
{
	/**
		Initialize PHPMailer.
	*/

	public function __construct()
	{
		$this->PluginDir = WEE_PATH . 'vendor/phpmailer/';
	}

	/**
		Load body and headers from the specified template file.
		Unknown headers are skipped.

		@param $sTemplate	The template name.
		@param $aData		Data to be used in the template.
	*/

	public function loadTemplate($sTemplate, array $aData = array())
	{
		$oTpl = new weeEmailTemplate($sTemplate, $aData);
		$this->Body = $oTpl->toString();

		foreach ($oTpl->aHeaders as $sName => $sValue)
			if (isset($this->$sName) && !is_array($this->$sName))
				$this->$sName = $sValue;
	}
}
