<?php

if ($oLDAP->isEqual('cn=Luke Skywalker, ou=people, dc=example, dc=com', 'userpassword', $sCryptedPass))
	doSomething();
else
	printError('Wrong password.');
