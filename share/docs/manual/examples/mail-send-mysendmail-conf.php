<?php

class mySendMail extends weeSendMail
{
	public function __construct()
	{
		parent::__construct();

		$this->From = weeApp()->cnf('mail.from');
		$this->FromName = weeApp()->cnf('mail.from.name');
		$this->Mailer = weeApp()->cnf('mail.method');
		$this->Sender = weeApp()->cnf('mail.from');
	}
}
