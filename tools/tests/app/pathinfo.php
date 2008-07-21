<?php

$_SERVER['REQUEST_URI']			= '';
$_SERVER['QUERY_STRING']		= '';
$_SERVER['PATH_INFO']			= '/foo/bar';

$this->isEqual('/foo/bar', weeApplication::getPathInfo(),
	'The path info is not the one from $_SERVER superglobal');

$_SERVER['PATH_INFO']			= null;
$_SERVER['REDIRECT_URL']		= 'fake data';
$_SERVER['SCRIPT_NAME']			= '/test/suite.php';
$_SERVER['PHP_SELF']			= $_SERVER['SCRIPT_NAME'] . '/foo/bar';

$this->isEqual('/foo/bar', weeApplication::getPathInfo(),
	'The path info cannot be guessed from SCRIPT_NAME and PHP_SELF when PATH_INFO is not available and the request is a redirection.');

$_SERVER['REQUEST_URI']			.= '?';

$this->isEqual('/foo/bar?', weeApplication::getPathInfo(),
	'Final interrogation mark is not included in the path info when explicitely set with an empty request string.');

$_SERVER['REDIRECT_URL']		= null;
$sBaseRequestUri				= $_SERVER['SCRIPT_NAME'] . '/foo/bar';
$_SERVER['REQUEST_URI']			= $sBaseRequestUri;

$this->isEqual('/foo/bar', weeApplication::getPathInfo(),
	'The path info cannot be guessed from REQUEST_URI and SCRIPT_NAME when PATH_INFO is not available.');

$_SERVER['QUERY_STRING']		= '?test=true';
$_SERVER['REQUEST_URI']			= $sBaseRequestUri . $_SERVER['QUERY_STRING'];

$this->isEqual('/foo/bar', weeApplication::getPathInfo(),
	'The request string can not be stripped off the path info.');

$_SERVER['QUERY_STRING']		= '';
$_SERVER['REQUEST_URI']			= $sBaseRequestUri . '?';

$this->isEqual('/foo/bar?', weeApplication::getPathInfo(),
	'Final interrogation mark is not included in the path info when explicitely set with an empty request string when PATH_INFO is not available.');
