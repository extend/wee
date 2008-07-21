<?php

/**
	Web:Extend
	Copyright (c) 2007 Dev:Extend

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
	Path to the wee application configuration file.
*/

if (!defined('WEE_CONF_FILE'))
	define('WEE_CONF_FILE', ROOT_PATH . 'app/conf/wee.cnf');

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

	protected $oAliases;

	/**
		Configuration for this application.
	*/

	protected $oConfig;

	/**
		The frame object that will be displayed.
	*/

	protected $oFrame;

	/**
		Modules loaded by the application.
	*/

	protected $aModules = array();

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
		{
			define('DEBUG', 1);

			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 1);
		}

		// Load aliases file

		if (!empty($this->oConfig['aliases.file']))
		{
			$sFile = $this->oConfig['aliases.file'];
			if (substr($sFile, 0, 2) == '//')
				$sFile = ROOT_PATH . substr($sFile, 2);

			$this->oAliases = new weeAliasesFile($sFile);
		}

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

		// Load mail settings

		if (!empty($this->oConfig['mail.debug.to']))
			define('WEE_MAIL_DEBUG_TO', $this->oConfig['mail.debug.to']);

		if (!empty($this->oConfig['mail.debug.reply-to']))
			define('WEE_MAIL_DEBUG_REPLY_TO', $this->oConfig['mail.debug.reply-to']);

		// Load output driver

		if (!empty($this->oConfig['start.output']))
			call_user_func(array($this->oConfig['output.driver'], 'select'));

		// Define cache settings

		if (!empty($this->oConfig['output.cache.path']))
			define('CACHE_PATH',	$this->oConfig['output.cache.path']);
		if (!empty($this->oConfig['output.cache.expire']))
			define('CACHE_EXPIRE',	$this->oConfig['output.cache.expire']);

		// Load database driver

		if (!empty($this->oConfig['start.db']))
		{
			$s = $this->oConfig['db.driver'];
			$this->aModules['db'] = new $s(array(
				'host'		=> $this->oConfig['db.host'],
				'user'		=> $this->oConfig['db.user'],
				'password'	=> $this->oConfig['db.password'],
				'dbname'	=> $this->oConfig['db.name'],
			));
		}

		// Start session

		if (!defined('WEE_CLI') && !empty($this->oConfig['start.session']))
		{
			$s = $this->oConfig['session.driver'];
			$this->aModules['session'] = new $s(
				(empty($this->aModules['db'])) ? null : $this->aModules['db'],
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
		Return the module given in parameter.

		@param	$name	Name of the module
		@return	object	The module object
	*/

	public function __get($name)
	{
		fire(empty($this->aModules[$name]), 'UnexpectedValueException', 'The module ' .$name . ' do not exist.');
		return $this->aModules[$name];
	}

	/**
		Return whether the given module is loaded.

		@param	$name	Name of the module
		@return	bool	Whether the module is loaded
	*/

	public function __isset($name)
	{
		return !empty($this->aModules[$name]);
	}

	/**
		Inform the application that we want to cache the output of the frame.

		The parameter is an array of variable defining how to cache the output.
		The only accepted variable currently is 'expire', defining the number of seconds
		the cache file will be valid. The default value is defined by the constant
		CACHE_EXPIRE, or 300 seconds if this constant is not found.

		@param $aParams Array of variables defining how to cache the output.
	*/

	public function cacheEvent($aParams = array())
	{
		if (empty($aParams['expire']))
			$aParams['expire'] = (defined('CACHE_EXPIRE')) ? CACHE_EXPIRE : 300;

		$this->aCacheParams = $aParams;
	}

	/**
		Clear all the cache files that matches the specified events.

		There is an additional event option used only by clearCache: query_string.
		It is what would fit in an urldecoded $_SERVER['QUERY_STRING'],
		that is the part after ? in an url, but with the ? INCLUDED.

		Giving the ? alone would delete only the cache file with no query string present,
		while giving ?query_string would delete only ?query_string. Giving nothing or
		giving an empty string would delete all the cache files for the specified frame, name and pathinfo.

		@param $aEvent The event for which cache will be deleted. Uses frame, name, pathinfo and query_string parameters. Frame is mandatory.
	*/

	public function clearCache($aEvent)
	{
		fire(empty($aEvent['frame']), 'InvalidParameterException', 'The frame name must be given to clear its cache.');
		$aEvent = array('name' => null, 'pathinfo' => null, 'query_string' => null) + $aEvent;

		$sCache = CACHE_PATH . $aEvent['frame'];
		if (!empty($aEvent['name']))
		{
			$sCache .= '/' . $aEvent['name'];
			if (!empty($aEvent['pathinfo']))
				$sCache .= $aEvent['pathinfo'];
		}
		$sCache .= '/' . $aEvent['query_string'];

		if (is_dir($sCache))
			rmdir_recursive($sCache);
		else
			@unlink($sCache);
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
			- name: name of the event
			- get: $_GET array for this event
			- post: $_POST array for this event
			- pathinfo: the PATH_INFO if any
			- noframechange: if defined and true, the frame of this event won't be displayed

		@param $aEvent Event information
	*/

	public function dispatchEvent($aEvent)
	{
		$oFrame = $this->loadFrame($aEvent['frame']);

		if (empty($aEvent['noframechange']))
			$this->oFrame = $oFrame;

		$oFrame->dispatchEvent($aEvent);
	}

	/**
		Returns the frame currently being processed.

		@return weeFrame The frame being processed
	*/

	public function getFrame()
	{
		return $this->oFrame;
	}

	/**
		Returns the path information with some path translation.
		The path information is the text after the file and before the query string in an URI.
		Example: http://example.com/my.php/This_is_the_path_info/Another_level/One_more?query_string

		@return	string The path information
	*/

	public static function getPathInfo()
	{
		$sPathInfo = null;

		if (isset($_SERVER['PATH_INFO']))
			$sPathInfo = $_SERVER['PATH_INFO'];
		elseif (isset($_SERVER['REDIRECT_URL']))
			$sPathInfo = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));

		if ($sPathInfo !== null)
		{
			// We found the path info from either PATH_INFO or PHP_SELF server variables.

			if (empty($_SERVER['QUERY_STRING']) && substr($_SERVER['REQUEST_URI'], -1) == '?')
				// If the query string is empty, but that an interrogation mark has been
				// explicitely included in the request URI, we keep it.
				$sPathInfo .= '?';

			return $sPathInfo;
		}

		// The path info begins after the script name part of the request URI.
		$sPathInfo = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));

		if (!empty($_SERVER['QUERY_STRING']))
		{
			// We need to remove the query string from the path info.
			$i = strlen($_SERVER['QUERY_STRING']);
			if (substr($sPathInfo, -$i) == $_SERVER['QUERY_STRING'])
				$sPathInfo = substr($sPathInfo, 0, -$i);
		}

		return urldecode($sPathInfo);
	}

	/**
		Returns an instance of the weeApplication singleton.

		@return weeApplication The weeApplication object for this process
	*/

	public static function instance()
	{
		if (!isset(self::$oSingleton))
			self::$oSingleton = new self;

		return self::$oSingleton;
	}

	/**
		Return whether the event is cached.

		If the event is cached, this method will add a value to the $aEvent
		event array, 'cache', to give the path to the cache file to output.

		@param [IN, OUT] $aEvent Event data given by weeApplication::translateEvent.
	*/

	protected function isEventCached(&$aEvent)
	{
		if (defined('NO_CACHE') || !defined('CACHE_PATH'))
			return false;

		$sCacheFilename = CACHE_PATH . $aEvent['frame'] . '/' . $aEvent['name'] . $aEvent['pathinfo'] . '/?' . urldecode($_SERVER['QUERY_STRING']);

		if (!file_exists($sCacheFilename))
			return false;

		$iTime = filemtime($sCacheFilename);

		if ($iTime === false)
			return false;

		if ($iTime < time())
			return false;

		$aEvent['cache'] = $sCacheFilename;

		return true;
	}

	/**
		Load and initialize the specified frame.

		@param	$sFrame		Frame's class name
		@return	weeFrame	The frame created
	*/

	protected function loadFrame($sFrame)
	{
		fire(!class_exists($sFrame), 'UnexpectedValueException', 'The frame ' . $sFrame . ' do not exist.');
		$oFrame = new $sFrame;
		fire(!($oFrame instanceof weeFrame), 'UnexpectedValueException', 'The frame ' . $sFrame . ' must be an instance of weeFrame.');

		$oFrame->setController($this);

		return $oFrame;
	}

	/**
		Entry point for a wee application.

		It translate the event sent by the browser, then
			if there is a cache file for this event, just output it and quit
			otherwise dispatch the event to the frame and display it.

		Before displaying it, if the frame requested it, the output will be
		stored in a file for later use for caching. The request is made by
		calling weeApp()->cacheEvent() from within the frame.
	*/

	public function main()
	{
		$aEvent = $this->translateEvent();

		if ($this->isEventCached($aEvent))
			readfile($aEvent['cache']);
		else
		{
			$this->dispatchEvent($aEvent);

			weeOutput::instance()->start();
			$sOutput = $this->oFrame->toString();

			if (!empty($this->aCacheParams) && defined('CACHE_PATH'))
			{
				$sCachePath = CACHE_PATH . $aEvent['frame'] . '/' . $aEvent['name'] . $aEvent['pathinfo'] . '/';
				$sCacheFilename = $sCachePath . '?' . urldecode($_SERVER['QUERY_STRING']);

				if (!is_dir($sCachePath))
					mkdir($sCachePath);

				file_put_contents($sCacheFilename, $sOutput);
				touch($sCacheFilename, time() + $this->aCacheParams['expire']);
			}

			echo $sOutput;
		}
	}

	/**
		Translate the event sent by the browser.

		@return array Event information
		@see weeApplication::dispatchEvent for event details
	*/

	protected function translateEvent()
	{
		$aEvent = array(
			//TODO:sometimes we may want to only accept xmlhttprequest when the
			//request comes from a user who we know is using this application,
			//and not some random other webserver using it for its own purpose...
			'context' => (array_value($_SERVER, 'HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ? 'xmlhttprequest' : 'http',

			'get'	=> $_GET,
			'post'	=> $_POST,

			'name'		=> null,
			'pathinfo'	=> null,
		);

		$sPathInfo = substr(self::getPathInfo(), 1);

		if (empty($sPathInfo))
			return array('frame' => (isset($this->oConfig['app.toppage'])) ? $this->oConfig['app.toppage'] : 'toppage') + $aEvent;

		if (isset($this->oAliases))
			$sPathInfo = $this->oAliases->resolveAlias($sPathInfo);

		$i = strpos($sPathInfo, '/');
		if ($i === false)
			return array('frame' => $sPathInfo) + $aEvent;

		$aEvent['frame']	= substr($sPathInfo, 0, $i);
		$sPathInfo			= substr($sPathInfo, $i + 1);

		$i = strpos($sPathInfo, '/');
		if ($i === false)
			return array('name' => $sPathInfo) + $aEvent;

		$aEvent['name']		= substr($sPathInfo, 0, $i);
		$aEvent['pathinfo']	= substr($sPathInfo, $i + 1);

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
