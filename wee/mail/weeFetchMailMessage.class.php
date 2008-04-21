<?php

/*
	Web:Extend
	Copyright (c) 2008 Dev:Extend

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
	Handle fetched email messages.
*/

class weeFetchMailMessage extends weeDataSource implements ArrayAccess
{
	/**
		Message header information.
	*/

	protected $oHeader;

	/**
		Link resource for this message's mailbox.
	*/

	protected $rLink;

	/**
		Message identifier in the mailbox.
	*/

	protected $iMsg;

	/**
		Number of attachments to this message.
	*/

	protected $iNbAttachments;

	/**
		Initialize the message object.

		@param $rLink Link resource for this message's mailbox.
		@param $iMsg Message identifier in the mailbox.
	*/

	public function __construct($rLink, $iMsg)
	{
		$this->rLink	= $rLink;
		$this->iMsg		= $iMsg;
	}

	/**
		@return array(weeFetchMailAttachment) The attachments to this message.
	*/

	public function getAttachments()
	{
		$oParts = imap_fetchstructure($this->rLink, $this->iMsg)->parts;

		if (count($oParts) == 0)
			return array();

		$aAttachments = array();

		foreach ($oParts as $iKey => $oPart)
			if ($oPart->type && ($oPart->ifdisposition == 0 || strtoupper($oPart->disposition) == 'ATTACHMENT'))
			{
				$sData = imap_fetchbody($this->rLink, $this->iMsg, $iKey + 1);
				if ($oPart->encoding == 3)
					$sData = base64_decode($sData);
				elseif ($oPart->encoding == 4)
					$sData = quoted_printable_decode($sData);

				$aAttachments[] = new weeFetchMailAttachment($oPart->parameters[0]->value, $sData);
			}

		return $aAttachments;
	}

	/**
		@return string This message's contents.
	*/

	public function getBody()
	{
		return imap_fetchbody($this->rLink, $this->iMsg, 1);
	}

	/**
		@return int The number of files attached to this message.
	*/

	public function numAttachments()
	{
		if (!is_null($this->iNbAttachments))
			return $this->iNbAttachments;

		$oParts = imap_fetchstructure($this->rLink, $this->iMsg)->parts;
		$this->iNbAttachments = 0;

		if (count($oParts) != 0)
			foreach ($oParts as $iKey => $oPart)
				if ($oPart->type && ($oPart->ifdisposition == 0 || strtoupper($oPart->disposition) == 'ATTACHMENT'))
					$this->iNbAttachments++;

		return $this->iNbAttachments;
	}

	/**
		Returns whether offset exists.

		@param	$offset	Offset name.
		@return	bool	Whether the offset exists.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetExists($offset)
	{
		if (is_null($this->oHeader))
			$this->oHeader = imap_headerinfo($this->rLink, $this->iMsg);

		return isset($this->oHeader->$offset);
	}

	/**
		Returns value at given offset.

		@param	$offset	Offset name.
		@return	bool	value at given offset
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetGet($offset)
	{
		if (is_null($this->oHeader))
			$this->oHeader = imap_headerinfo($this->rLink, $this->iMsg);

		fire(!isset($this->oHeader->$offset), 'InvalidArgumentException',
			'The value for offset ' . $offset . ' was not found in the data.');

		if ($this->bMustEncodeData)
			return weeOutput::encodeValue($this->oHeader->$offset);
		return $this->oHeader->$offset;
	}

	/**
		Sets a new value for the given offset.

		@param	$offset	Offset name.
		@param	$value	New value for this offset.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetSet($offset, $value)
	{
		burn('BadMethodCallException', 'This array access is read-only.');
	}

	/**
		Unsets offset.

		@param	$offset	Offset name.
		@see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	*/

	public function offsetUnset($offset)
	{
		burn('BadMethodCallException', 'This array access is read-only.');
	}
}
