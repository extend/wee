<?php

/*
	Web:Extend
	Copyright (c) 2006-2010 Dev:Extend

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
*/

abstract class weeDocumentor implements Mappable, Printable
{
	/**
		Stores parsed classes data.
	*/

	protected $aClasses	= array();

	/**
		Stores parsed funcs data.
	*/

	protected $aFuncs	= array();

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
			'type'			=> $oClass->isInterface() ? 'interface' : ($oClass->isAbstract() ? 'abstract' : ($oClass->isFinal() ? 'final' : null)),

			'filename'		=> substr($oClass->getFileName(), strlen(getcwd()) + 1),
			'startline'		=> $oClass->getStartLine(),
			'endline'		=> $oClass->getEndLine(),

			'consts'		=> $oClass->getConstants(),
		);

		// Get DocComment data

		$this->parseDocComment($this->trimDocComment($oClass->getDocComment()), $aParsedData);
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
			$aMethod = array(
				'name'			=> $o->getName(),
				'type'			=> $o->isStatic() ? 'static' : ($o->isAbstract() ? 'abstract' : ($o->isFinal() ? 'final' : null)),
				'visibility'	=> $o->isPublic() ? 'public' : ($o->isPrivate() ? 'private' : 'protected'),
				'internal'		=> $o->isInternal(),

				'startline'		=> $o->getStartLine(),
				'endline'		=> $o->getEndLine(),

				'numreqparams'	=> $o->getNumberOfRequiredParameters(),
			);

			if (!$aMethod['internal'])
				$aHints = self::getParametersTypeHints($o);

			$sFilename = substr($o->getFileName(), strlen(getcwd()) + 1);
			if (!empty($sFilename) && $sFilename != $a['filename'])
				$aMethod['filename'] = $sFilename;

			// Get DocComment data

			$this->parseDocComment($this->trimDocComment($o->getDocComment()), $aParsedData);
			$aMethod = array_merge($aMethod, $aParsedData);

			if (!empty($aFunc['return']))
				$aFunc['return']['ref'] = $o->returnsReference();

			$aMethod['params'] = array();
			foreach ($o->getParameters() as $oParameter)
			{
				$aParameter = array(
					'name'		=> $oParameter->getName(),
					'ref'		=> $oParameter->isPassedByReference(),
					'null'		=> $oParameter->allowsNull(),
					'default'	=> $oParameter->isDefaultValueAvailable() ? var_export($oParameter->getDefaultValue(), true) : null,
				);

				if (isset($aMethod['paramscomment'][$aParameter['name']]))
					$aParameter['comment'] = $aMethod['paramscomment'][$aParameter['name']];

				if ($aMethod['internal'])
				{
					if ($oParameter->isArray())
						$aParameter['hint'] = 'array';
				}
				else
				{
					$aParameter['type'] = $this->getVariableType($aParameter['name']);
					if (isset($aHints[$aParameter['name']]))
						$aParameter['hint'] = $aHints[$aParameter['name']];
				}

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
			);

			// Get DocComment data

			$this->parseDocComment($this->trimDocComment($o->getDocComment()), $aParsedData);
			$aProperty = array_merge($aProperty, $aParsedData);

			$a['properties'][] = $aProperty;
		}

		// Sort child arrays

		usort($a['implements'], 'weeDocumentor::nameCmp');
		usort($a['methods'], 'weeDocumentor::nameCmp');
		usort($a['properties'], 'weeDocumentor::nameCmp');

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

		$aFunc = array(
			'name'			=> $o->getName(),
			'filename'		=> substr($o->getFileName(), strlen(getcwd()) + 1),
			'startline'		=> $o->getStartLine(),
			'endline'		=> $o->getEndLine(),
			'numreqparams'	=> $o->getNumberOfRequiredParameters(),
		);

		$aHints = self::getParametersTypeHints($o);

		$this->parseDocComment($this->trimDocComment($o->getDocComment()), $aParsedData);
		$aFunc = array_merge($aFunc, $aParsedData);
		if (!empty($aFunc['return']))
			$aFunc['return']['ref'] = $o->returnsReference();

		$aFunc['params'] = array();
		foreach ($o->getParameters() as $oParameter)
		{
			$aParameter = array(
				'name'		=> $oParameter->getName(),
				'ref'		=> $oParameter->isPassedByReference(),
				'null'		=> $oParameter->allowsNull(),
				'type'		=> $this->getVariableType($oParameter->getName()),
				'default'	=> $oParameter->isDefaultValueAvailable() ? var_export($oParameter->getDefaultValue(), true) : null,
			);

			if (isset($aHints[$aParameter['name']]))
				$aParameter['hint'] = $aHints[$aParameter['name']];

			if (isset($aFunc['paramscomment'][$aParameter['name']]))
				$aParameter['comment'] = $aFunc['paramscomment'][$aParameter['name']];

			$aFunc['params'][] = $aParameter;
		}

		unset($aFunc['paramscomment']);
		$this->aFuncs[] = $aFunc;

		return $this;
	}

	/**
		Returns the type-hints of the parameters of a given function.

		@param	$oFunction					The function to scan.
		@return	array(string)				An associative array mapping parameters' names to their type-hint.
	*/

	public static function getParametersTypeHints(ReflectionFunctionAbstract $oFunction)
	{
		$bArg			= true;
		$bFunctionName	= false;
		$iLevel			= 0;
		$bInPrototype	= false;
		$sHint			= null;
		$aHints			= array();

		$oFile = new SplFileObject($oFunction->getFileName());
		$oFile->seek($oFunction->getStartLine() - 1);
		while ($oFile->valid())
		{
			$aTokens	= token_get_all('<?php ' . $oFile->current() . ' */');
			$iCount		= count($aTokens);

			for ($i = 0; $i < $iCount; ++$i)
			{
				if ($bInPrototype)
				{
					if (is_array($aTokens[$i]) && $bArg == true)
						switch ($aTokens[$i][0])
						{
							case T_STRING:
							case T_ARRAY:
								$sHint = $aTokens[$i][1];
								break;

							case T_VARIABLE:
								if ($sHint !== null)
									$aHints[substr($aTokens[$i][1], 1)] = $sHint;

								$bArg	= false;
								$sHint	= null;
						}
					elseif ($aTokens[$i] == '(')
						++$iLevel;
					elseif ($aTokens[$i] == ')')
					{
						if(--$iLevel == 0)
							break 2;
					}
					elseif ($iLevel == 1 && $aTokens[$i] == ',')
						$bArg = true;
				}
				elseif (is_array($aTokens[$i]))
					switch ($aTokens[$i][0])
					{
						case T_FUNCTION:
							$bFunctionName = true;
							break;

						case T_STRING:
							if ($bFunctionName)
							{
								if ($aTokens[$i][1] == $oFunction->getName())
									$bInPrototype = true;
								else
									$bFunctioName = false;
							}
					}
			}

			$oFile->next();
		}

		return $aHints;
	}

	/**
		Returns the type of a variable from its name.

		@param	$sVariable					The name of the variable.
		@return	string						The type of the variable.
		@throw	InvalidArgumentException	$sVariable is not a valid variable name.
	*/

	protected function getVariableType($sVariable)
	{
		is_string($sVariable) and isset($sVariable[0])
			or burn('InvalidArgumentException',
				_WT('$sVariable is not a valid variable name.'));

		static $aTypes = array(
			'a' => 'array',
			'b' => 'bool',
			'e' => 'exception',
			'f' => 'float',
			'i' => 'int',
			'm' => 'mixed',
			'o' => 'object',
			'r' => 'resource',
			's' => 'string',
		);

		return array_value($aTypes, $sVariable[0]);
	}

	/**
		Compare names like strcasecmp. Used internally to sort classes and functions.

		@param $a1 The first array.
		@param $a2 The second array.
		@return int Returns < 0 if a1['name'] is less than a2['name'] ; > 0 if a1['name'] is greater than a2['name'] , and 0 if they are equal.
	*/

	protected static function nameCmp($a1, $a2)
	{
		return strcasecmp($a1['name'], $a2['name']);
	}

	/**
		Parse docComments to retrieve the comment and the modifiers.

		@param $sDocComment The docComment.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocComment($sDocComment, &$aParsedData)
	{
		$aParsedData = array();

		// Whether there is stars on each line
		// This docComment format is only scarcely supported for now
		$bHasStars = preg_match('/^[[:space:]]*\*/', $sDocComment) != 0;

		$a = explode("\n", $sDocComment);
		$sDocComment = null;

		foreach ($a as $sLine)
		{
			if (empty($sLine) || $sLine[0] != '@' || ($sLine[1] == ' ' || $sLine[1] == "\t")) {
				if ($bHasStars && strpos($sLine, '@') === false) // Leave stars if it's a @modifier to have a bullet list
					$sLine = substr($sLine, strpos($sLine, '*') + 1);

				$sDocComment .= $sLine . "\n";
			} else {
				$sLine	= substr($sLine, 1);
				$a		= preg_split('/\s+/', $sLine, 2);
				$sFunc	= 'parseDocComment' . ucwords($a[0]);
				$sLine	= isset($a[1]) ? $a[1] : '';

				if (is_callable(array($this, $sFunc)))
					$this->$sFunc($sLine, $aParsedData);
				else
					weeLog(sprintf(_WT('Skipping Unknown doc comment modifier "%s".'), $a[0]));
			}
		}

		$aParsedData['comment'] = trim($sDocComment);
	}

	/**
		Parse a @bug line from the docComment.

		@param $sLine The @bug line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentBug($sLine, &$aParsedData)
	{
		if (empty($aParsedData['bugs']))
			$aParsedData['bugs'] = array();

		$aParsedData['bugs'][] = $sLine;
	}

	/**
		Parse a @deprecated line from the docComment.

		@param $sLine The @overload line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentDeprecated($sLine, &$aParsedData)
	{
		$aParsedData['deprecated'] = $sLine;
	}

	/**
		Parse a @overload line from the docComment.

		@param $sLine The @overload line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentOverload($sLine, &$aParsedData)
	{
		if (empty($aParsedData['overloads']))
			$aParsedData['overload'] = array();

		$iPos = strpos($sLine, ')');
		$iPos === false and burn('UnexpectedValueException',
			_WT('The overloaded method prototype does not have a closing parenthesis.'));

		$sFunc		= substr($sLine, 0, $iPos);
		$sComment	= trim(substr($sLine, $iPos + 1));

		$aParsedData['overloads'][] = array('func' => $sFunc, 'comment' => $sComment);
	}

	/**
		Parse a @param line from the docComment.

		@param $sLine The @param line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentParam($sLine, &$aParsedData)
	{
		if (empty($aParsedData['paramscomment']))
			$aParsedData['paramscomment'] = array();

		$sLine		= substr($sLine, 1);
		$a			= preg_split('/\s+/', $sLine, 2);

		if (isset($a[1]))
			$aParsedData['paramscomment'][$a[0]] = $a[1];
	}

	/**
		Parse the @return line from the docComment.

		As opposed to other docComment modifiers, there can be only one return line.
		If more than one is found, only the last one is used.

		@param $sLine The @return line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentReturn($sLine, &$aParsedData)
	{
		$aParsedData['return']	= array();
		$a						= preg_split('/\s+/', $sLine, 2);

		$aParsedData['return']['type'] = $a[0];
		if (isset($a[1]))
			$aParsedData['return']['comment'] = $a[1];
	}

	/**
		Parse a @see line from the docComment.

		@param $sLine The @see line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentSee($sLine, &$aParsedData)
	{
		if (empty($aParsedData['sees']))
			$aParsedData['sees'] = array();

		$aParsedData['sees'][] = $sLine;
	}

	/**
		Parse a @throw line from the docComment.

		@param $sLine The @throw line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentThrow($sLine, &$aParsedData)
	{
		if (empty($aParsedData['throws']))
			$aParsedData['throws'] = array();

		$aParsedData['throws'][] = $sLine;
	}

	/**
		Parse a @todo line from the docComment.

		@param $sLine The @todo line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentTodo($sLine, &$aParsedData)
	{
		if (empty($aParsedData['todos']))
			$aParsedData['todos'] = array();

		$aParsedData['todos'][] = $sLine;
	}

	/**
		Parse a @warning line from the docComment.

		@param $sLine The @warning line.
		@param $aParsedData Array where the parsed data is saved.
	*/

	protected function parseDocCommentWarning($sLine, &$aParsedData)
	{
		if (empty($aParsedData['warnings']))
			$aParsedData['warnings'] = array();

		$aParsedData['warnings'][] = $sLine;
	}

	/**
		@return array Array containing all the classes and functions metadata.
	*/

	public function toArray()
	{
		return array(
			'classes'	=> $this->aClasses,
			'funcs'		=> $this->aFuncs,
		);
	}

	/**
		Trim the docComment.

		This will remove the comment operators, as well as a number of
		tabulations from the beginning of each line equal to the number
		of tabulations before the text on the first line.

		@param $sDocComment The docComment.
		@return string Trimmed docComment.
	*/

	protected function trimDocComment($sDocComment)
	{
		$sDocComment = substr($sDocComment, 3, -2);	// Remove comment operators.
		$sDocComment = ltrim($sDocComment, "\r\n");	// Strip the beginning of linebreaks.
		$sDocComment = rtrim($sDocComment);			// Strip the end of whitespaces.

		if (!empty($sDocComment))
		{
			for ($i = 0; !empty($sDocComment[$i]) && $sDocComment[$i] == "\t"; $i++)
				;

			$sDocComment = str_replace("\n" . str_repeat("\t", $i), "\n", "\n" . $sDocComment);
		}

		return $sDocComment;
	}
}
