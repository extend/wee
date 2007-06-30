<?php

if (!defined('ALLOW_INCLUSION')) die;

/**
	Path to the wee application configuration file.
*/

if (!defined('WEE_CONF_FILE'))
	define('WEE_CONF_FILE', 'conf/wee.cnf');

/**
	Main class of a wee application.

	This class basically translate events and redirect them.
	It also loads a configuration file and acts as a central point
	for various application components, like database and session.
*/

class weeApplication implements Singleton
{
	/**
		Aliases for weeFrame objects.
	*/

	protected $oAliases = array();

	/**
		Configuration for this application.
	*/

	protected $oConfig;

	/**
		The frame object that will be displayed.
	*/

	protected $oFrame;

	/**
		Instance of the current singleton.
		There can only be one.
	*/

	protected static $oSingleton;

	/**
		Load the configuration file WEE_CONF_FILE and initialize
		automatically the various components of the application.
	*/

	protected function __construct()
	{
		// Load ini file and do include/commons

		$this->oConfig = new weeFileConfig(WEE_CONF_FILE);

		// Activate debug mode if needed

		if (!defined('DEBUG') && !empty($this->oConfig['debug.mode']))
			define('DEBUG', 1);

		// Load aliases file

		if (!empty($this->oConfig['aliases.file']))
			$this->oAliases = new weeFileConfig($this->oConfig['aliases.file']);

		// Load default timezone

		if (!empty($this->oConfig['timezone']))
			date_default_timezone_set($this->oConfig['timezone']);

		// Add path to autoload
		// - All path are separated by : like in PATH environment variable
		// - A // in the path means ROOT_PATH

		if (!empty($this->oConfig['autoload.path']))
		{
			$aPath = explode(':', $this->oConfig['autoload.path']);
			foreach ($aPath as $s)
				weeAutoload::addPath(str_replace('//', ROOT_PATH, $s));
		}

		// Load output driver

		if (!empty($this->oConfig['start.output']))
			call_user_func(array($this->oConfig['output.driver'], 'instance'));

		// Load database driver

		if (!empty($this->oConfig['start.db']))
		{
			$s = $this->oConfig['db.driver'];
			$GLOBALS['Db'] = new $s(array(//TODO:not globals
				'host'		=> $this->oConfig['db.host'],
				'user'		=> $this->oConfig['db.user'],
				'password'	=> $this->oConfig['db.password'],
				'dbname'	=> $this->oConfig['db.name'],
			));
		}

		// Start session

		if (!empty($this->oConfig['start.session']))
		{
			$s = $this->oConfig['session.driver'];
			$GLOBALS['Session'] = new $s(//TODO:not globals
				$this->oConfig['session.db.table'],
				$this->oConfig['session.db.field.key'],
				$this->oConfig['session.db.field.username'],
				$this->oConfig['session.db.field.password']
			);
		}
	}

	/**
		Because there can only be one application object, we disable cloning.
	*/

	final private function __clone()
	{
	}

	/**
		Get a configuration value.

		@param	$sName	Name of the configuration parameter
		@return	mixed	Value of this configuration parameter
	*/

	public static function cnf($sName)
	{
		$aConfig = self::instance()->oConfig;

		if (empty($aConfig[$sName]))
			return null;
		return $aConfig[$sName];
	}

	/**
		Dispatch an event to its respective frame.

		Event information can contain the following parameters:
			- context: either http or xmlhttprequest
			- frame: name of the destination frame
			- event: name of the event
			- get: $_GET array for this event
			- post: $_POST array for this event
			- pathinfo: the PATH_INFO if any
			- noframechange: if defined and true, the frame of this event won't be displayed

		@param $aEvent Event information
	*/

	public function dispatchEvent($aEvent)
	{
		$oFrame = $this->loadFrame($aEvent['frame']);
		$oFrame->dispatchEvent($aEvent);

		if (empty($aEvent['noframechange']))
			$this->oFrame = $oFrame;
	}

	/**
		Returns the path information with some path translation.
		The path information is the text after the file and before the query string in an URI.
		Example: http://example.com/my.php/This_is_the_path_info/Another_level/One_more?query_string

		@param	$bRemoveQueryString	You can keep the query string by setting this variable to false
		@return	string				The path information
	*/

	public static function getPathInfo($bRemoveQueryString = true)
	{
		$sRequestURI = str_replace('/./', '/', $_SERVER['REQUEST_URI']);
		if (substr($sRequestURI, 0, 2) == './')
			$sRequestURI = substr($sRequestURI, 2);

		$sPathInfo = urldecode(substr($sRequestURI, 1 + strlen($_SERVER['SCRIPT_NAME'])));

		if ($bRemoveQueryString && !empty($_SERVER['QUERY_STRING']) && substr($sPathInfo, -strlen($_SERVER['QUERY_STRING'])) == $_SERVER['QUERY_STRING'])
			$sPathInfo = substr($sPathInfo, 0, -1 - strlen($_SERVER['QUERY_STRING']));

		return $sPathInfo;
	}

	/**
		Returns an instance of the weeApplication singleton.

		@return weeApplication The weeApplication object for this process
	*/

	public static function instance()
	{
		if (!isset(self::$oSingleton))
		{
			$s = __CLASS__;
			self::$oSingleton = new $s;
		}

		return self::$oSingleton;
	}

	/**
		Load and initialize the specified frame.

		@param	$sFrame		Frame's class name
		@return	weeFrame	The frame created
	*/

	protected function loadFrame($sFrame)
	{
		//TODO:better error when class not found... like a 404 error

		if (empty($this->oAliases[$sFrame]))
			$oFrame = new $sFrame;
		else
		{
			$s = $this->oAliases[$sFrame];
			$oFrame = new $s;
		}

		fire(!($oFrame instanceof weeFrame));

		$oFrame->setController($this);

		return $oFrame;
	}

	/**
		Entry point for a wee application.

		Basically translate event sent by browser,
		dispatch it to the frame, and then
		display the frame.
	*/

	public function main()
	{
		$this->dispatchEvent($this->translateEvent());
		echo $this->oFrame->toString();
	}

	/**
		Translate the event sent by the browser.

		@return array Event information
		@see weeApplication::dispatchEvent for event details
	*/

	protected function translateEvent()
	{
		$aEvent = array();

		$aEvent['context']	= 'http'; //TODO:xmlhttprequest detection
		$aEvent['get']		= $_GET;
		$aEvent['post']		= $_POST;

		$sPathInfo	= self::getPathInfo();

		if (empty($sPathInfo))
			return $aEvent + array('frame' => 'index');

		$i = strpos($sPathInfo, '/');
		if ($i === false)
			return $aEvent + array('frame' => $sPathInfo);
		else
			$aEvent['frame'] = substr($sPathInfo, 0, $i);

		$sPathInfo	= substr($sPathInfo, $i + 1);

		//TODO:try to see if using strpos directly in substr is fine
		$i = strpos($sPathInfo, '/');
		if ($i === false)
			$aEvent['event'] = $sPathInfo;
		else
			$aEvent['event'] = substr($sPathInfo, 0, $i);

		$aEvent['pathinfo'] = substr($sPathInfo, $i + 1);

		return $aEvent;
	}
}

/**
	Shortcut to weeApplication::instance().
*/

function weeApp()
{
	return weeApplication::instance();
}

?>
