<?php

/*
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
	Builds an XML based on PHP code.

	TODO:Handles the following docComments commands:
	- @bug comment
	- @overload func($arg, ...) comment
	- @param $arg comment
	- @return type comment
	- @todo comment
	- @warning comment
*/

class weeDocumentor implements Printable
{
	/**
		Maps elements to their childs.
		Used by arrayToXML ONLY.
	*/

	protected $aSpecialChilds = array(
		'bugs'			=> 'bug',
		'classes'		=> 'class',
		'funcs'			=> 'func',
		'implements'	=> 'implement',
		'methods'		=> 'method',
		'overloads'		=> 'overload',
		'params'		=> 'param',
		'properties'	=> 'property',
		'sees'			=> 'see',
		'todos'			=> 'todo',
		'warnings'		=> 'warning'
	);

	/**
		Stores parsed classes data.
	*/

	protected $aClasses	= array();

	/**
		Stores parsed funcs data.
	*/

	protected $aFuncs	= array();

	/**
		Recursive method that converts specified array to XML.
		It maps the element name to the property $aSpecialChilds.

		@param	$a		Array to convert.
		@param	$sName	Name of the current element.
		@return	string	The XML generated from the array.
	*/

	protected function arrayToXML(array $a, $sName)
	{
		$s = '<' . $sName . '>';

		foreach ($a as $sKey => $mValue)
		{
			if (empty($mValue))
				continue;

			if (is_array($mValue))
			{
				if (!empty($this->aSpecialChilds[$sName]))
					$sNextName = $this->aSpecialChilds[$sName];
				else
					$sNextName = $sKey;

				$s .= $this->arrayToXML($mValue, $sNextName);
			}
			else
			{
				if (!empty($this->aSpecialChilds[$sName]))
					$sKey = $this->aSpecialChilds[$sName];

				$s .= '<' . $sKey . '>' . htmlspecialchars($mValue) . '</' . $sKey . '>';
			}
		}

		return $s . '</' . $sName . '>';
	}

	/**
		Tells weeDocumentor to get data of the specified class.
		The class must be declared before calling this method.

		@param	$sClassName	The class to get data from.
		@return	$this		For chained calls.
	*/

	public function docClass($sClassName)
	{
		$oClass = new ReflectionClass($sClassName);

		$a = array(
			'name'			=> $oClass->getName(),
			'parent'		=> $oClass->getParentClass() == null ? null : $oClass->getParentClass()->getName(),
			'type'			=> $oClass->isInterface() ? 'interface' : ($oClass->isAbstract() ? 'abstract' : ($oClass->isFinal() ? 'final' : 'class')),

			'filename'		=> $oClass->getFileName(),
			'startline'		=> $oClass->getStartLine(),
			'endline'		=> $oClass->getEndLine(),

			'doccomment'	=> $this->trimDocComment($oClass->getDocComment()),
			'constants'		=> $oClass->getConstants(),
		);

		// Get DocComment data

		$a['doccomment'] = $this->parseDocComment($a['doccomment'], $aParsedData);
		$a = array_merge($a, $aParsedData);

		// Interfaces

		$aImplement			= $oClass->getInterfaces();
		$a['implements']	= array();
		foreach ($aImplement as $o)
			$a['implements'][] = $o->getName();

		// Methods

		$aMethods		= $oClass->getMethods();
		$a['methods']	= array();
		foreach ($aMethods as $o)
		{
			$aMethod	= array(
				'name'			=> $o->getName(),
				'type'			=> $o->isStatic() ? 'static' : ($o->isAbstract() ? 'abstract' : ($o->isFinal() ? 'final' : 'method')),
				'visibility'	=> $o->isPublic() ? 'public' : ($o->isPrivate() ? 'private' : 'protected'),
				'internal'		=> $o->isInternal(),

				'startline'		=> $o->getStartLine(),
				'endline'		=> $o->getEndLine(),

				'returnsref'	=> $o->returnsReference(),
				'numparams'		=> $o->getNumberOfParameters(),
				'numrequired'	=> $o->getNumberOfRequiredParameters(),

				'doccomment'	=> $this->trimDocComment($o->getDocComment()),
			);

			// Get DocComment data

			$aMethod['doccomment'] = $this->parseDocComment($aMethod['doccomment'], $aParsedData);
			$aMethod = array_merge($aMethod, $aParsedData);

			$aMethod['params'] = array();
			foreach ($o->getParameters() as $oParameter)
			{
				$aParameter = array(
					'name'		=> $oParameter->getName(),
					'ref'		=> $oParameter->isPassedByReference(),
					'array'		=> $oParameter->isArray(),
					'null'		=> $oParameter->allowsNull(),
					'optional'	=> $oParameter->isOptional(),
					'default'	=> $oParameter->isDefaultValueAvailable() ? var_export($oParameter->getDefaultValue(), true) : null,
				);

				if ($aParameter['default'] == 'NULL')
					$aParameter['default'] = 'null';

				if (isset($aMethod['paramscomment'][$aParameter['name']]))
					$aParameter['comment'] = $aMethod['paramscomment'][$aParameter['name']];

				$aMethod['params'][] = $aParameter;
			}

			unset($aMethod['paramscomment']);
			$a['methods'][] = $aMethod;
		}

		// Properties

		$aProperties		= $oClass->getProperties();
		$a['properties']	= array();
		foreach ($aProperties as $o)
		{
			$aProperty		= array(
				'name'			=> $o->getName(),
				'static'		=> $o->isStatic(),
				'visibility'	=> $o->isPublic() ? 'public' : ($o->isPrivate() ? 'private' : 'protected'),
				'doccomment'	=> $this->trimDocComment($o->getDocComment()),
			);

			// Get DocComment data

			$aProperty['doccomment'] = $this->parseDocComment($aProperty['doccomment'], $aParsedData);
			$aProperty = array_merge($aProperty, $aParsedData);

			$a['properties'][] = $aProperty;
		}

		// Finally store the data

		$this->aClasses[] = $a;

		return $this;
	}

	/**
		Loads all files in path with filename that finishes with CLASS_EXT, and get class data.

		@param	$sPath	The path to get class data from.
		@return	$this	For chained calls.
	*/

	public function docClassFromPath($sPath)
	{
		$oDir = new RecursiveDirectoryIterator($sPath);
		foreach (new RecursiveIteratorIterator($oDir) as $oFilename)
		{
			if (substr($oFilename, -strlen(CLASS_EXT)) != CLASS_EXT)
				continue;

			require_once((string)$oFilename);
			$this->docClass(basename($oFilename, CLASS_EXT));
		}

		return $this;
	}

	/**
		Tells weeDocumentor to get data of the specified function.
		The function must be declared before calling this method.

		@param	$sFunctionName	The function to get data from.
		@return	$this			For chained calls.
	*/

	public function docFunc($sFunctionName)
	{
		$o = new ReflectionFunction($sFunctionName);

		$aFunc	= array(
			'name'			=> $o->getName(),

			'startline'		=> $o->getStartLine(),
			'endline'		=> $o->getEndLine(),

			'returnsref'	=> $o->returnsReference(),
			'numparams'		=> $o->getNumberOfParameters(),
			'numrequired'	=> $o->getNumberOfRequiredParameters(),

			'doccomment'	=> $this->trimDocComment($o->getDocComment()),
		);

		// Get DocComment data

		$aFunc['doccomment'] = $this->parseDocComment($aFunc['doccomment'], $aParsedData);
		$aFunc = array_merge($aFunc, $aParsedData);

		$aFunc['params'] = array();
		foreach ($o->getParameters() as $oParameter)
		{
			$aParameter = array(
				'name'		=> $oParameter->getName(),
				'ref'		=> $oParameter->isPassedByReference(),
				'array'		=> $oParameter->isArray(),
				'null'		=> $oParameter->allowsNull(),
				'optional'	=> $oParameter->isOptional(),
				'default'	=> $oParameter->isDefaultValueAvailable() ? var_export($oParameter->getDefaultValue(), true) : null,
			);

			if ($aParameter['default'] == 'NULL')
				$aParameter['default'] = 'null';

			if (isset($aFunc['paramscomment'][$aParameter['name']]))
				$aParameter['comment'] = $aFunc['paramscomment'][$aParameter['name']];

			$aFunc['params'][] = $aParameter;
		}

		unset($aFunc['paramscomment']);
		$this->aFuncs[]	= $aFunc;

		return $this;
	}

	protected function parseDocComment($sDocComment, &$aParsedData)
	{
		$aParsedData = array();

		$a = explode("\n", $sDocComment);
		$sDocComment = null;

		foreach ($a as $sLine)
		{
			if (strlen($sLine) < 2)
				continue;

			if ($sLine[0] != '@' || ($sLine[1] == ' ' || $sLine[1] == "\t"))
				$sDocComment .= $sLine . "\r\n";
			else
			{
				$sLine	= substr($sLine, 1);
				$a		= preg_split('/\s+/', $sLine, 2);
				$sFunc	= 'parseDocComment' . ucwords($a[0]);
				$sLine	= isset($a[1]) ? $a[1] : '';

				$this->$sFunc($sLine, $aParsedData);
			}
		}
		$aParsedData['doccomment'] = trim($sDocComment);
	}

	protected function parseDocCommentBug($sLine, &$aParsedData)
	{
		if (empty($aParsedData['bugs']))
			$aParsedData['bugs'] = array();

		$aParsedData['bugs'][] = $sLine;
	}

	protected function parseDocCommentOverload($sLine, &$aParsedData)
	{
		if (empty($aParsedData['overloads']))
			$aParsedData['overload'] = array();

		$iPos = strpos($sLine, ')');
		fire($iPos === false, 'UnexpectedValueException',
			'The overloaded method prototype does not have a closing parenthese');

		$sFunc		= substr($sLine, 0, $iPos);
		$sComment	= trim(substr($sLine, $iPos + 1));

		$aParsedData['overloads'][] = array('function' => $sFunc, 'comment' => $sComment);
	}

	protected function parseDocCommentParam($sLine, &$aParsedData)
	{
		if (empty($aParsedData['paramscomment']))
			$aParsedData['paramscomment'] = array();

		$sLine		= substr($sLine, 1);
		$a			= preg_split('/\s+/', $sLine, 2);

		if (isset($a[1]))
			$aParsedData['paramscomment'][$a[0]] = $a[1];
	}

	protected function parseDocCommentReturn($sLine, &$aParsedData)
	{
		$aParsedData['return']	= array();
		$a						= preg_split('/\s+/', $sLine, 2);

		$aParsedData['return']['what'] = $a[0];
		if (isset($a[1]))
			$aParsedData['return']['comment'] = $a[1];
	}

	protected function parseDocCommentSee($sLine, &$aParsedData)
	{
		if (empty($aParsedData['sees']))
			$aParsedData['sees'] = array();

		$aParsedData['sees'][] = $sLine;
	}

	protected function parseDocCommentTodo($sLine, &$aParsedData)
	{
		if (empty($aParsedData['todos']))
			$aParsedData['todos'] = array();

		$aParsedData['todos'][] = $sLine;
	}

	protected function parseDocCommentWarning($sLine, &$aParsedData)
	{
		if (empty($aParsedData['warnings']))
			$aParsedData['warnings'] = array();

		$aParsedData['warnings'][] = $sLine;
	}

	/**
		Builds and prints an XML from parsed data.

		@return string The XML generated by this class.
	*/

	public function toString()
	{
		$s  = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n";
		$s .= <<<EOF

<!--
	Web:Extend
	Copyright (c) 2006 Dev:Extend

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
-->


EOF;

		$s .= '<wee>';

		if (!empty($this->aClasses))
			$s .= $this->arrayToXML($this->aClasses, 'classes');

		if (!empty($this->aFuncs))
			$s .= $this->arraytoXML($this->aFuncs, 'funcs');

		return $s . '</wee>';
	}

	protected function trimDocComment($sDocComment)
	{
		return preg_replace('/^[\t ]*/m', '', trim(substr($sDocComment, 3, -2)));
	}
}

?>
