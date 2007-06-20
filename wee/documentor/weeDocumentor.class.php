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
		'classes'		=> 'class',
		'funcs'			=> 'func',
		'implements'	=> 'implement',
		'methods'		=> 'method',
		'params'		=> 'param',
		'properties'	=> 'property',
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

				$s .= '<' . $sKey . '>' . $mValue . '</' . $sKey . '>';
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

			'doccomment'	=> $this->trimDocComment($oClass->getDocComment()),//TODO:decompose
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

				'doccomment'	=> $this->trimDocComment($o->getDocComment()),//TODO:decompose
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

				$aMethod['params'][] = $aParameter;
			}

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
				'doccomment'	=> $o->getDocComment(),//TODO:decompose
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

			'doccomment'	=> $o->getDocComment(),//TODO:decompose
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

			$aFunc['params'][] = $aParameter;
		}

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
				$sFunc	= 'parseDocComment' . strtok($sLine, " \t");
				$sLine	= preg_replace('/^\w+[\t ]+/', '', $sLine);

				$this->$sFunc($sLine, $aParsedData);
			}
		}
	}

	protected function parseDocCommentBug($sLine, &$aParsedData)
	{
		if (empty($aParsedData['bugs']))
			$aParsedData['bugs'] = array();

		$aParsedData['bugs'] = $sLine;
	}

	protected function parseDocCommentOverload($sLine, &$aParsedData)
	{
	}

	protected function parseDocCommentParam($sLine, &$aParsedData)
	{
	}

	protected function parseDocCommentReturn($sLine, &$aParsedData)
	{
	}

	protected function parseDocCommentSee($sLine, &$aParsedData)
	{
	}

	protected function parseDocCommentTodo($sLine, &$aParsedData)
	{
	}

	protected function parseDocCommentWarning($sLine, &$aParsedData)
	{
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
