<?php

/*
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
	Fetch mail from IMAP and POP3 mailboxes.
*/

class weeFetchMail
{
	/**
		Link resource for this mailbox.
	*/

	protected $rLink;

	/**
		Initialize the mailbox connection.

		The mailbox connection parameters are as follow:
		- host:		Host name of the server hosting the mailbox. Default: localhost.
		- port:		Port of the imap service. Default: 143.
		- mailbox:	Mailbox name. Default: INBOX.
		- flags:	Connection flags. Optionnal.
		- user:		User name.
		- password:	User password.

		For a detailed list of available flags, please see the PHP documentation
		for imap_open.

		The connection to the mailbox is read-only until tested enough.

		@param $aParams Mailbox connection parameters.
		@see http://php.net/imap_open
	*/

	public function __construct($aParams)
	{
		function_exists('imap_open') or burn('ConfigurationException',
			_WT('The IMAP PHP extension is required by the weeFetchMail class.'));

		empty($aParams['user']) and burn('InvalidParameterException',
			_WT('The user name was not provided in the connection parameters.'));
		empty($aParams['password']) and burn('InvalidParameterException',
			_WT('The user password was not provided in the connection parameters.'));

		// Fill in the default values
		$aParams = $aParams + array(
			'host'		=> 'localhost',
			'port'		=> 143,
			'mailbox'	=> 'INBOX',
		);

		$sConnection = '{' . $aParams['host'] . ':' . $aParams['port'];
		if (!empty($aParams['flags']))
			$sConnection .= $aParams['flags'];
		$sConnection .= '}' . $aParams['mailbox'];

		$this->rLink = @imap_open($sConnection, $aParams['user'], $aParams['password'], OP_READONLY, 1);
		$this->rLink === false and burn('UnexpectedValueException', _WT('Failed to open the mailbox.'));
	}

	/**
		Fetch all the messages from the mailbox.

		@return array(weeFetchMailMessage) All the messages from the mailbox.
	*/

	public function fetchAll()
	{
		$aResults = array();
		$iTotal = imap_num_msg($this->rLink);

		for ($iMsg = 1; $iMsg <= $iTotal; $iMsg++)
			$aResults[] = new weeFetchMailMessage($this->rLink, $iMsg);

		return $aResults;
	}

	/**
		Performs a search in the mailbox.

		The criteria is the same as described in the PHP documentation for the
		parameter 'criteria' to the imap_search function.
			@see http://php.net/imap_search

		@param $sCriteria Search query.
		@return array(weeFetchMailMessage) Messages matching the search criteria.
	*/

	public function search($sCriteria)
	{
		$aResults = imap_search($this->rLink, $sCriteria);

		if ($aResults === false)
			return array();

		foreach ($aResults as $iKey => $iMsg)
			$aResults[$iKey] = new weeFetchMailMessage($this->rLink, $iMsg);

		return $aResults;
	}
}
