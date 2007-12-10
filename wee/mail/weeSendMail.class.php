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

require_once(WEE_PATH . 'vendor/phpmailer/class.phpmailer.php');

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

	/**
		Send the mail.

		You can configure various settings for debugging purposes,
		which will change the behaviour of this class when DEBUG is defined.
			- WEE_MAIL_DEBUG_TO:		all mails will be sent to this address
			- WEE_MAIL_DEBUG_REPLY_TO:	sets the reply-to value to this address

		If DEBUG is defined but WEE_MAIL_DEBUG_TO isn't, this method will just
		return true without doing anything, allowing the test of other portions
		of the program without doing the actual send of the email.

		WEE_MAIL_DEBUG_REPLY_TO can be an email of the QA engineering team who
		can receive all the complaints about emails during the test phase, for
		example.
	*/

	public function send()
	{
		if (defined('DEBUG'))
		{
			if (!defined('WEE_MAIL_DEBUG_TO'))
				return true;

			// Remove the destinations and change the "to" address to the debug one

			$this->to	= array();
			$this->cc	= array();
			$this->bcc	= array();

			$this->addAddress(WEE_MAIL_DEBUG_TO);

			// Change the "reply-to" address if a debug one is provided

			if (defined('WEE_MAIL_DEBUG_REPLY_TO'))
			{
				$this->ReplyTo = array();
				$this->addReplyTo(WEE_MAIL_DEBUG_REPLY_TO);
			}

			//TODO:add debug infos to the email body?
		}

		return parent::send();
	}
}

?>
