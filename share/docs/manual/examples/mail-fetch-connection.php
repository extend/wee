<?php

$oMailbox = new weeFetchMail(array(
	'host'		=> 'imap.example.org',
	'flags'		=> '/imap/tls',
	'user'		=> 'imyme@example.org',
	'password'	=> 'hunter2',
));
