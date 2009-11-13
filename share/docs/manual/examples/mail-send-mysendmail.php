<?php

class mySendMail extends weeSendMail
{
	public $Mailer = 'sendmail';
	public $From = 'sendmail@example.org';
	public $FromName = 'This is an example email';
	public $Sender = 'sendmail@example.org';
}
