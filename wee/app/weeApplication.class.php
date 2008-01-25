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
			define('DEBUG', 1);

		// Load aliases file

		if (!empty($this->oConfig['aliases.file']))
			$this->oAliases = new weeFileConfig(
				str_replace('//', ROOT_PATH, $this->oConfig['aliases.file']));

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

		if (!empty($this->oConfig['start.session']))
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
		if (defined('DEBUG'))
			weeLog('weeApplication::dispatchEvent ' . print_r($aEvent, true));

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

		@return	string				The path information
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

			if (empty($_SERVER['REQUEST_STRING']) && substr($_SERVER['REQUEST_URI'], -1) == '?')
				// If the request string is empty, but that an interrogation mark has been
				// explicitely included in the request URI, we keep it.
				$sPathInfo .= '?';

			return $sPathInfo;
		}

		// The path info begins after the script name part of the request URI.
		$sPathInfo = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));

		if (!empty($_SERVER['REQUEST_STRING']))
		{
			// We need to remove the query string from the path info.
			$i = strlen($_SERVER['REQUEST_STRING']);
			if (substr($sPathInfo, -$i) == $_SERVER['REQUEST_STRING'])
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
		Load and initialize the specified frame.

		@param	$sFrame		Frame's class name
		@return	weeFrame	The frame created
	*/

	protected function loadFrame($sFrame)
	{
		if (!empty($this->oAliases[$sFrame]))
			$sFrame = $this->oAliases[$sFrame];

		fire(!class_exists($sFrame), 'UnexpectedValueException', 'The frame ' . $sFrame . ' do not exist.');
		$oFrame = new $sFrame;
		fire(!($oFrame instanceof weeFrame), 'UnexpectedValueException', 'The frame ' . $sFrame . ' must be an instance of weeFrame.');

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

		weeOutput::instance()->start();
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

		//TODO:sometimes we may want to only accept xmlhttprequest when the
		//request comes from a user who we know is using this application,
		//and not some random other webserver using it for its own purpose...
		$aEvent['context'] = (array_value($_SERVER, 'HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') ? 'xmlhttprequest' : 'http';

		$aEvent['get']	= $_GET;
		$aEvent['post']	= $_POST;

		$sPathInfo = substr(self::getPathInfo(), 1);

		if (empty($sPathInfo))
			return $aEvent + array('frame' => (!empty($this->oConfig['app.toppage'])) ? $this->oConfig['app.toppage'] : 'toppage');

		$i = strpos($sPathInfo, '/');
		if ($i === false)
			return $aEvent + array('frame' => $sPathInfo);
		else
			$aEvent['frame'] = substr($sPathInfo, 0, $i);

		$sPathInfo	= substr($sPathInfo, $i + 1);

		//TODO:try to see if using strpos directly in substr is fine
		$i = strpos($sPathInfo, '/');
		if ($i === false)
		{
			$aEvent['event']	= $sPathInfo;
			$aEvent['pathinfo']	= '';
		}
		else
		{
			$aEvent['event']	= substr($sPathInfo, 0, $i);
			$aEvent['pathinfo']	= substr($sPathInfo, $i + 1);
		}

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
