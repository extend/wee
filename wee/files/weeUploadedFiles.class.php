<?php

/*
	Dev:Extend Web Library
	Copyright (c) 2006 Dev:Extend

	This software is licensed under the Dev:Extend Public License. You can use,
	copy, modify and/or distribute the software under the terms of this License.

	This software is distributed WITHOUT ANY WARRANTIES, including, but not
	limited to, the implied warranties of MERCHANTABILITY and FITNESS FOR A
	PARTICULAR PURPOSE. See the Dev:Extend Public License for more details.

	You should have received a copy of the Dev:Extend Public License along with
	this software; if not, you can download it at the following url:
	http://dev-extend.eu/license/.
*/

if (!defined('ALLOW_INCLUSION')) die;

if (!defined('UPLOAD_ERR_NO_TMP_DIR'))	define('UPLOAD_ERR_NO_TMP_DIR',	6);
if (!defined('UPLOAD_ERR_CANT_WRITE'))	define('UPLOAD_ERR_CANT_WRITE', 7);

class weeUploadedFile
{
	public $sSrcName;
	public $sTmpName;
	public $sMimeType;
	public $iSize;
	public $iErrorCode;

	public function __construct($sSrcName, $sTmpName, $sMimeType, $iSize, $iErrorCode)
	{
		$this->sSrcName		= $sSrcName;
		$this->sTmpName		= $sTmpName;
		$this->sMimeType	= $sMimeType;
		$this->iSize		= $iSize;
		$this->iErrorCode	= $iErrorCode;
	}

	public function fileExists($sDestination, $sNewFilename = null)
	{
		if (empty($sNewFilename))
			$sNewFilename	= $this->getFilename();

		return is_file($sDestination . '/' . $sNewFilename);
	}

	//TODO:user friendly errors
	public function getError()
	{
		switch ($this->iErrorCode)
		{
			case UPLOAD_ERR_OK:
				Burn('IllegalStateException');

			case UPLOAD_ERR_INI_SIZE:
				return _('The uploaded file size exceeds the upload_max_filesize directive in php.ini.');

			case UPLOAD_ERR_FORM_SIZE:
				return _('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');

			case UPLOAD_ERR_PARTIAL:
				return _('The uploaded file was only partially uploaded.');

			case UPLOAD_ERR_NO_FILE:
				return _('No file was uploaded.');

			case UPLOAD_ERR_NO_TMP_DIR:
				return _('Missing a temporary folder.');

			case UPLOAD_ERR_CANT_WRITE:
				return _('Failed to write file to disk.');

			default:
				return _('Unknown error.');
		}
	}


	public function getExt()
	{
		$m	= strrchr($this->GetFilename(), '.');
		if ($m === false)
			return null;
		return substr($m, 1);
	}

	public function getFilename()
	{
		return basename($this->sSrcName);
	}

	public function isOK()
	{
		return $this->iErrorCode == UPLOAD_ERR_OK;
	}

	public function moveTo($sDestination, $sNewFilename = null)
	{
		clearstatcache();
		Fire(!is_dir($sDestination), 'InvalidArgumentException');
		Fire(!is_uploaded_file($this->sTmpName), 'UnexpectedValueException');

		if (empty($sNewFilename))
			$sNewFilename	= $this->getFilename();

		return move_uploaded_file($this->sTmpName, $sDestination . '/' . $sNewFilename);
	}
}

class weeUploadedFiles implements Iterator
{
	protected		$bFilterValid;
	protected		$sFilter;
	protected		$bChild;
	protected		$iCurrentChild;

	protected function createFile($a)
	{
		//TODO:handle case when $a['name'] is an array
		return new weeUploadedFile($a['name'], $a['tmp_name'], $a['type'], $a['size'], $a['error']);
	}

	protected function createFileFromChild($a)
	{
		return new weeUploadedFile($a['name'][$this->iCurrentChild], $a['tmp_name'][$this->iCurrentChild],
			$a['type'][$this->iCurrentChild], $a['size'][$this->iCurrentChild], $a['error'][$this->iCurrentChild]);
	}

	public function exists($sName)
	{
		if (empty($_FILES[$sName]))
			return false;

		if (is_array($_FILES[$sName]['name']))
			return !empty($_FILES[$sName]['name'][0]);
		return !empty($_FILES[$sName]['name']);
	}

	public function filter($sName)
	{
		$this->bFilterValid	= array_key_exists($sName, $_FILES);
		$this->sFilter		= $sName;
		return $this;
	}

	public function get($sName)
	{
		Fire(empty($_FILES[$sName]) || is_array($_FILES[$sName]['name']), 'InvalidArgumentException');
		return $this->createFile($_FILES[$sName]);
	}

	public function isEmpty()
	{
		return empty($_FILES);
	}

	// Iterator

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

	public function key()
	{
		if (empty($this->sFilter))	return key($_FILES);
		else						return $this->sFilter;
	}

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
				$this->bFilterValid	= false;
		}
	}

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
					$this->bChild	= false;
					return (next($_FILES) !== false);
				}
			}

			return (current($_FILES) !== false);
		}
		else
		{
			if ($this->bChild)
				return (array_key_exists($this->iCurrentChild, $_FILES[$this->sFilter]['name']));
			else
				return $this->bFilterValid;
		}
	}
}

?>
