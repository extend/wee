<?php

/**
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

if (!defined('UPLOAD_ERR_NO_TMP_DIR'))	define('UPLOAD_ERR_NO_TMP_DIR', 6);
if (!defined('UPLOAD_ERR_CANT_WRITE'))	define('UPLOAD_ERR_CANT_WRITE', 7);

/**
	A better handling of files uploaded using forms.
*/

class weeUploads implements Iterator
{
	/**
		True if the filter specified by $sFilter is valid, false otherwise.
		If it is not valid, it will be ignored.
	*/

	protected $bFilterValid;

	/**
		Name of the file to filter.
	*/

	protected $sFilter;

	/**
		True if we are inside an array of files sharing the same name, false otherwise.
	*/

	protected $bChild;

	/**
		Index of the child we are on, if applicable.
	*/

	protected $iCurrentChild;

	/**
		Returns a new weeUploadedFile object based on the array given in parameter.

		@param	$a				An array containing the file details.
		@return	weeUploadedfile	A new file object containing the details given.
	*/

	protected function createFile($a)
	{
		return new weeUploadedFile($a['name'], $a['tmp_name'], $a['type'], $a['size'], $a['error']);
	}

	/**
		Returns a new weeUploadedFile object based on the array given in parameter.
		This method works on arrays of files of the same name, and returns one of the child.
		Used when iterating through all the files or an array of files sharing the same name.

		@param	$a				An array containing the file details.
		@return	weeUploadedfile	A new file object containing the details given.
	*/

	protected function createFileFromChild($a)
	{
		return new weeUploadedFile($a['name'][$this->iCurrentChild], $a['tmp_name'][$this->iCurrentChild],
			$a['type'][$this->iCurrentChild], $a['size'][$this->iCurrentChild], $a['error'][$this->iCurrentChild]);
	}

	/**
		Return the current element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		if (empty($this->sFilter))
		{
			if ($this->bChild)	return $this->createFileFromChild(current($_FILES));
			else				return $this->createFile(current($_FILES));
		}
		else
		{
			if ($this->bChild)	return $this->createFileFromChild($_FILES[$this->sFilter]);
			else				return $this->createFile($_FILES[$this->sFilter]);
		}
	}

	/**
		Check if the given file exists.

		@param	$sName	The name of the file.
		@return	bool	True if the file exists, false otherwise.
	*/

	public function exists($sName)
	{
		if (empty($_FILES[$sName]))
			return false;

		if (is_array($_FILES[$sName]['name']))
			return !empty($_FILES[$sName]['name'][0]);
		return !empty($_FILES[$sName]['name']);
	}

	/**
		Returns the specified file.
		Throws an exception if the name given points to an array of files.

		@param	$sName	The name of the file.
		@return	weeUploadedfile	A new file object for the requested file.
	*/

	public function fetch($sName)
	{
		empty($_FILES[$sName]) and burn('InvalidArgumentException',
			sprintf(_WT('File "%s" was not found in the uploaded files.'), $sName));
		is_array($_FILES[$sName]['name']) and burn('InvalidArgumentException',
			sprintf(_WT('"%s" is an array of files. You cannot retrieve it using weeUploads::fetch.'), $sName));

		return $this->createFile($_FILES[$sName]);
	}

	/**
		Filters through the files uploaded.
		Use filter if you want to iterate only through an array of files sharing the same name.

		@param	$sName	The name of the file(s).
		@return	$this
	*/

	public function filter($sName = null)
	{
		$this->bFilterValid	= !is_null($sName) && array_key_exists($sName, $_FILES);
		$this->sFilter		= $sName;
		return $this;
	}

	/**
		Checks if there is no uploaded files.

		@return bool True if there is no uploaded files, false otherwise.
	*/

	public function isEmpty()
	{
		return empty($_FILES);
	}

	/**
		Return the key of the current element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		if (empty($this->sFilter))	return key($_FILES);
		else						return $this->sFilter;
	}

	/**
		Move forward to next element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		if ($this->bChild)
			$this->iCurrentChild++;
		else
		{
			if (empty($this->sFilter))
			{
				$a = next($_FILES);
				if (is_array($a['name']))
				{
					$this->bChild			= true;
					$this->iCurrentChild	= 0;
				}
			}
			else
				$this->bFilterValid = false;
		}
	}

	/**
		Rewind the Iterator to the first element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		$this->iCurrentChild = 0;

		if (empty($this->sFilter))
		{
			reset($_FILES);

			$a				= current($_FILES);
			$this->bChild	= is_array($a['name']);
		}
		else
			$this->bChild	= is_array($_FILES[$this->sFilter]['name']);
	}

	/**
		Check if there is a current element after calls to rewind() or next().

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		if (empty($this->sFilter))
		{
			if ($this->bChild)
			{
				$a	= current($_FILES);
				if (array_key_exists($this->iCurrentChild, $a['name']))
					return true;
				else
				{
					$this->bChild = false;
					return (next($_FILES) !== false);
				}
			}

			return (current($_FILES) !== false);
		}

		if ($this->bChild)
			return (array_key_exists($this->iCurrentChild, $_FILES[$this->sFilter]['name']));

		return $this->bFilterValid;
	}
}
