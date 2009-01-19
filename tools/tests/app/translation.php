<?php

class weeApplication_testTranslateEvent extends weeApplication
{
	// We need to expose the app configuration.
	public $aConfig = array();

	// We need a public constructor.
	public function __construct() {}

	// We are testing this method, expose it.
	public function translateEvent()
	{
		return parent::translateEvent();
	}
}

$aFormerServer	= $_SERVER;
$aFormerGet		= $_GET;
$aFormerPost	= $_POST;

try {
	$_SERVER = array(
		'PATH_INFO'			=> '',
		'REQUEST_METHOD'	=> 'GET',
		'REQUEST_STRING'	=> '?test=get',
		'REQUEST_URI'		=> '/test.php?test=get',
	);

	$_GET = array(
		'test'				=> 'get'
	);

	$_POST = array(
		'test'				=> 'post'
	);

	// toppage
	$o = new weeApplication_testTranslateEvent();
	$aEvent = $o->translateEvent();

	// Frames

	$this->isTrue(isset($aEvent['frame']),
		_WT('The event does not have a frame'));

	$this->isEqual('toppage', $aEvent['frame'],
		_WT('The default frame is not toppage.'));

	// Custom toppage
	$o->aConfig['app.toppage'] = 'pikachu';
	$aEvent = $o->translateEvent();
	$this->isEqual('pikachu', $aEvent['frame'],
		_WT('The event frame is not the configuration toppage frame.'));

	$this->isFalse(isset($aEvent['name']),
		_WT('The toppage event should not have a name.'));

	// Request a frame
	$_SERVER['REQUEST_URI']	= '/test.php/foo?test=get';
	$_SERVER['PATH_INFO']	= '/foo';
	$aEvent = $o->translateEvent();

	$this->isEqual('foo', $aEvent['frame'],
		_WT('The frame is not the one requested.'));

	$this->isFalse(isset($aEvent['name']),
		_WT('The event have a name.'));

	$this->isTrue(empty($aEvent['pathinfo']),
		_WT('The pathinfo is not empty.'));

	// Request a specific event...
	$_SERVER['REQUEST_URI']	= '/test.php/foo/bar?test=get';
	$_SERVER['PATH_INFO']	= '/foo/bar';
	$aEvent = $o->translateEvent();

	$this->isEqual('foo', $aEvent['frame'],
		_WT('The frame is not the one requested.'));

	$this->isTrue(isset($aEvent['name']),
		_WT('The application has not returned any name for the event.'));

	$this->isEqual('bar', $aEvent['name'],
		_WT('The event name is not correct.'));

	$this->isTrue(empty($aEvent['pathinfo']),
		_WT('The pathinfo is not empty.'));

	// ...with extra parts in PATH_INFO
	$_SERVER['REQUEST_URI']	= '/test.php/foo/bar/action/new?test=get';
	$_SERVER['PATH_INFO']	= '/foo/bar/action/new';
	$aEvent = $o->translateEvent();

	$this->isEqual('foo', $aEvent['frame'],
		_WT('The frame is not the one requested.'));

	$this->isTrue(isset($aEvent['name']),
		_WT('The application does not return the name of the event when there is some extra parts in the path info.'));

	$this->isEqual('bar', $aEvent['name'],
		_WT('The event name returned by the application is not correct when there is some extra parts in the path info.'));

	$this->isTrue(isset($aEvent['pathinfo']),
		_WT('The event has no pathinfo.'));

	$this->isEqual('action/new', $aEvent['pathinfo'],
		_WT('The pathinfo is not correct.'));

	// Request parameters

	$this->isTrue(isset($aEvent['post']['test']),
		_WT('The event does not include the POST request parameters.'));

	$this->isEqual('post', $aEvent['post']['test'],
		_WT('The event does not have the correct value of the POST request parameter given to the application.'));

	$this->isTrue(isset($aEvent['get']['test']),
		_WT('The event does not include the GET request parameters.'));

	$this->isEqual('get', $aEvent['get']['test'],
		_WT('The event does not have the correct value of the GET request parameter given to the application.'));

	if (!defined('WEE_CLI')) {
		// We cannot test context and method when running test suite from command line
		// as they depend on the WEE_CLI constant.

		// Context

		$this->isTrue(isset($aEvent['context']),
			_WT('The event does not have a context.'));

		$this->isEqual('http', $aEvent['context'],
			_WT('The default context is not http.'));

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$aEvent = $o->translateEvent();
		$this->isEqual('xmlhttprequest', $aEvent['context'],
			_WT('The event context is not xmlhttprequest even though the HTTP_X_REQUESTED_WITH server variable says so.'));

		// Request Method

		$this->isTrue(isset($aEvent['method']),
			_WT('weeApplication::translateEvent does not have a request method.'));

		$this->isEqual('get', $aEvent['method'],
			_WT('weeApplication::translateEvent does not return the expected request method.'));
	}
} catch (Exception $oException) {}

$_SERVER	= $aFormerServer;
$_GET		= $aFormerGet;
$_POST		= $aFormerPost;

if (isset($oException))
	throw $oException;
