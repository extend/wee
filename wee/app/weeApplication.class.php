<?php

/**
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

class weeApplication
{
	/**
		Configuration for this application.
	*/

	protected $aConfig;

	/**
		Drivers loaded by the application.
	*/

	protected $aDrivers = array();

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
		// Loads from cache if possible, otherwise tries to load the configuration file
		// and then, if cache is enabled, cache the configuration array

		if (!defined('DEBUG') && !defined('NO_CACHE') && defined('WEE_CONF_CACHE') && is_readable(WEE_CONF_CACHE))
			$this->aConfig = require(WEE_CONF_CACHE);
		else try {
			$oConfigFile = new weeConfigFile(WEE_CONF_FILE);
			$this->aConfig = $oConfigFile->toArray();

			if (!defined('DEBUG') && defined('WEE_CONF_CACHE'))
			{
				file_put_contents(WEE_CONF_CACHE, '<?php return ' . var_export($this->aConfig, true) . ';');
				chmod(WEE_CONF_CACHE, 0600);
			}
		} catch (FileNotFoundException $e) {
			// No configuration file. Stop here and display a friendly message.

			if (defined('WEE_CLI'))
				echo "The configuration file was not found.\nPlease consult the documentation for more information.\n";
			else {
				header('HTTP/1.0 500 Internal Server Error');
				require(ROOT_PATH . 'res/wee/noconfig.htm');
			}
			exit;
		}

		// Add path to autoload
		// - All path are separated by : like in PATH environment variable
		// - A // in the path means ROOT_PATH

		if (!empty($this->aConfig['app.autoload.path'])) {
			$aPath = explode(':', $this->aConfig['app.autoload.path']);
			foreach ($aPath as $s)
				weeAutoload::addPath(str_replace('//', ROOT_PATH, $s));
		}

		// Mail settings

		if (!empty($this->aConfig['mail.debug.to']))
			define('WEE_MAIL_DEBUG_TO', $this->aConfig['mail.debug.to']);

		if (!empty($this->aConfig['mail.debug.reply-to']))
			define('WEE_MAIL_DEBUG_REPLY_TO', $this->aConfig['mail.debug.reply-to']);

		// Session settings

		if (!empty($this->aConfig['session.check.ip']))
			define('WEE_SESSION_CHECK_IP', 1);
		if (!empty($this->aConfig['session.check.token']))
			define('WEE_SESSION_CHECK_TOKEN', 1);

		// Timezone settings

		if (!empty($this->aConfig['app.timezone']))
			date_default_timezone_set($this->aConfig['app.timezone']);

		// Select output driver

		call_user_func(array($this->aConfig['output.driver'], 'select'));

		// Force selected drivers to start

		$aStart = $this->cnfArray('start');
		foreach ($aStart as $sName => $b)
			if (!empty($b))
				$this->__get($sName);
	}

	/**
		Because there can only be one application object, we disable cloning.
	*/

	final private function __clone()
	{
	}

	/**
		Return the given driver. The driver will first be loaded if it wasn't yet.

		@param	$name	Name of the driver
		@return	object	The driver object
	*/

	public function __get($sName)
	{
		if (empty($this->aDrivers[$sName])) {
			empty($this->aConfig[$sName . '.driver']) and burn('InvalidArgumentException',
				sprintf('The driver %s was not found in the configuration.', $sName));

			$aParams = $this->cnfArray($sName);
			unset($aParams['driver']); // Redundant, remove it

			$this->aDrivers[$sName] = new $this->aConfig[$sName . '.driver']($aParams);
		}

		return $this->aDrivers[$sName];
	}

	/**
		Get a configuration value.

		@param	$sName	Name of the configuration parameter
		@return	mixed	Value of this configuration parameter
	*/

	public function cnf($sName)
	{
		if (!array_key_exists($sName, $this->aConfig))
			return null;
		return $this->aConfig[$sName];
	}

	/**
		Get all matching configuration values.

		This method lets you get an array from the configuration file.
		The match is done on the beginning of the name path. As an example,
		if you need to retrieve all db.* values, you would pass 'db' as parameter.

		@param	$sPattern	Pattern to look for.
		@return	array		Array containing the resulting matches.
	*/

	public function cnfArray($sPattern)
	{
		$sPattern .= '.';
		$iLen = strlen($sPattern);
		$aMatch = array();

		foreach ($this->aConfig as $sName => $mValue)
			if (substr($sName, 0, $iLen) == $sPattern)
				$aMatch[substr($sName, $iLen)] = $mValue;

		return $aMatch;
	}

	/**
		Dispatch an event to its respective frame.

		Event information can contain the following parameters:
			- context: either http or xmlhttprequest
			- frame: name of the destination frame
			- method: request method used to access the event (e.g. GET, POST)
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

		if ($sPathInfo !== null) {
			// We found the path info from either PATH_INFO or PHP_SELF server variables.

			if (empty($_SERVER['QUERY_STRING']) && substr($_SERVER['REQUEST_URI'], -1) == '?')
				// If the query string is empty, but that an interrogation mark has been
				// explicitely included in the request URI, we keep it.
				$sPathInfo .= '?';

			return $sPathInfo;
		}

		// The path info begins after the script name part of the request URI.
		
		$iScriptLength	= strlen($_SERVER['SCRIPT_NAME']);
		$sName			= basename($_SERVER['SCRIPT_NAME']);
		$iNameLength	= strlen($sName);
		$sPathInfo		= substr($_SERVER['REQUEST_URI'], $iScriptLength - $iNameLength);

		if (substr($sPathInfo, 0, $iNameLength) == $sName)
			$sPathInfo	= substr($sPathInfo, $iNameLength);

		if (!empty($_SERVER['QUERY_STRING'])) {
			// We need to remove the query string from the path info.
			$i = strlen($_SERVER['QUERY_STRING']);
			if (substr($sPathInfo, -$i) == $_SERVER['QUERY_STRING'])
				$sPathInfo = substr($sPathInfo, 0, -$i - 1);
		}

		return urldecode($sPathInfo);
	}

	/**
		Returns an instance of the weeApplication singleton.

		At the time of the first call of this method, a shortcut function to this method called weeApp is created.

		@return weeApplication The weeApplication object for this process
	*/

	public static function instance()
	{
		if (self::$oSingleton === null) {
			static $iInstance = 0;
			$iInstance++ == 0 or
				burn('IllegalStateException',
					_WT('Trying to instanciate weeApplication within its own constructor. ') .
					_WT('This error can happen if you inherited a class created in the constructor ') .
					_WT('and put logic that uses weeApplication in it (models, for example).'));

			function weeApp() { return weeApplication::instance(); }
			self::$oSingleton = new self;
		}

		return self::$oSingleton;
	}

	/**
		Load and initialize the specified frame.

		@param	$sFrame						Frame's class name
		@return	weeFrame					The frame created
		@throw	UnexpectedValueException	The frame class does not exist or is not a subclass of weeFrame
	*/

	protected function loadFrame($sFrame)
	{
		@is_subclass_of($sFrame, 'weeFrame') or burn('RouteNotFoundException',
			sprintf(_WT('The frame %s does not exist.'), $sFrame));

		return new $sFrame($this);
	}

	/**
		Entry point for a wee application.

		It translates the event sent by the browser,
		then dispatch it to the frame and finally
		orders the frame to render the resulting view.
	*/

	public function main()
	{
		$aEvent = $this->translateEvent();

		$this->dispatchEvent($aEvent);

		if ($this->oFrame->getStatus() == weeFrame::UNAUTHORIZED_ACCESS) {
			if (defined('WEE_CLI'))
				echo _WT('You are not allowed to access the specified frame/event.'), "\n";
			else {
				header('HTTP/1.0 403 Forbidden');
				require(ROOT_PATH . 'res/wee/unauthorized.htm');
			}
			exit;
		}

		weeOutput::instance()->start(!empty($this->aConfig['output.gzip']));
		echo $this->oFrame->toString();
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
			'context'	=> (array_value($_SERVER, 'HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ? 'xmlhttprequest' : 'http',
			'method'	=> $_SERVER['REQUEST_METHOD'],

			'get'		=> $_GET,
			'post'		=> $_POST,

			'name'		=> null,
			'pathinfo'	=> null,
		);

		$sPathInfo = substr(self::getPathInfo(), 1);

		// Apply the locale found in the pathinfo if the locale module is started
		if (!empty($sPathInfo) && !empty($this->aDrivers['locale']))
			$sPathInfo = $this->aDrivers['locale']->setFromPathInfo($sPathInfo);

		// Use the toppage frame if the pathinfo is empty
		if (empty($sPathInfo))
			return array('frame' => (isset($this->aConfig['app.toppage'])) ? $this->aConfig['app.toppage'] : 'toppage') + $aEvent;

		// Apply custom routing
		$sPathInfo = $this->translateRoute($sPathInfo, $aEvent['get']);

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

	/**
		Apply custom routing.

		All the routes defined in the configuration file are tested in their given order.
		The translated route is returned as soon as there is a match.

		Note that this method is not called if the pathinfo is empty.

		If there is a new query string as a result of the custom routing,
		we parse it and add its values to the $aGet array.
		Note that values already present in the $aGet array will be overwritten
		if they share the same name as these new parameters

		A RouteNotFoundException is thrown when the route cannot be found
		and the configuration variable 'routing.strict' is true.

		@param $sPathInfo The pathinfo before routing
		@param $aGet The GET array this method will write to if additional parameters are found after translating
		@return string The pathinfo after the routing translation
	*/

	protected function translateRoute($sPathInfo, &$aGet)
	{
		$aRoutes = $this->cnfArray('route');
		$iCount = 0;

		foreach ($aRoutes as $sPattern => $sRoute) {
			$sPattern = '/^' . str_replace('/', '\/', $sPattern) . '$/i';
			$sTranslatedRoute = preg_replace($sPattern, $sRoute, $sPathInfo, 1, $iCount);

			if ($iCount > 0) {
				$aRoute = explode('?', $sTranslatedRoute, 2);

				if (sizeof($aRoute) > 1) {
					$aNewGet = array();
					parse_str($aRoute[1], $aNewGet);
					$aGet = $aNewGet + $aGet;
				}

				return $aRoute[0];
			}
		}

		array_value($this->aConfig, 'routing.strict') and burn('RouteNotFoundException',
			_WT('The route could not be found for this URL.'));

		return $sPathInfo;
	}
}
