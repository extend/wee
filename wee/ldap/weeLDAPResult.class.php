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
	Class for entries results.
*/

class weeLDAPResult implements Iterator
{
	/**
		LDAP link identifier. 
	*/

	protected $rLink;

	/**
		LDAP link identifier of the result. 
	*/

	protected $rResult;

	/**
		LDAP link identifier of the current entry. 
	*/

	protected $rEntry;

	//~ /**
		//~ LDAP link identifier of the reference for the current entry. 
	//~ */

	//~ protected $rReference;

	/**
		Key of the current iterated element.
	*/

	protected $iCurrentIndex;

	/**
		Initialise the WeeLDAPResult object.

		@param $rLink The connection link identifier 
		@param $rResult The search result link identifier
	*/

	public function __construct($rLink, $rResult)
	{
		$this->rLink = $rLink;
		$this->rResult = $rResult;

		$this->rEntry = ldap_first_entry($this->rLink, $this->rResult);
		if ($this->rEntry === false)
			throw new LDAPException(
				_WT('Failed to get the first entry.') . "\n" . ldap_error(), 
				ldap_errno($this->rLink)
			);
	}

	/**
		Free up the memory allocated internally to store the result.
	*/

	public function __destruct()
	{
		ldap_free_result($this->rResult);
	}

	/**
		Return the the current element.

		@return weeLDAPEntry An instance of weeLDAPEntry.
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function current()
	{
		return new weeLDAPEntry($this->rLink, $this->rEntry);
	}

	/**
		Fetch the next entry.

		@return weeLDAPEntry An instance of weeLDAPEntry.
	*/

	public function fetch()
	{
		$this->rewind();
		return $this->current();
	}

	/**
		Fetch all entries from the current result.

		weeLDAPResult::search read also the attributes and multiple values.
		The structure of the returned value is as follow :

			$aEntries['count'] = Number of entries in the result.
			$aEntries[i]['count'] = Number of attributes in the ith entry, also $aEntries[i]['count'].
			$aEntries[i][j]['count'] = Number of values for the jth attribute in ith entry, also $aEntries[i]['attribute']['count'].

			$aEntries[i] = The ith entry in the result.
			$aEntries[i][j] = The jth attributes in the ith entry, also $aEntries[i]['attribute'].
			$aEntries[i][j][k] = The kth value of the jth attribute in the ith entry, also $aEntries[i]['attribute'][k].

		@return array An instance of weeLDAPEntry.
	*/

	public function fetchAll()
	{
		$aEntries = ldap_get_entries($this->rLink, $this->rResult);
		if ($aEntries === false)
			throw new LDAPException(
				_WT('weeLDAPResult::fetchAll failed to get entries.') . "\n" . ldap_error(),
				ldap_errno($this->rLink)
			);

		return $aEntries;
	}

	/**
		Return the key of the current element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function key()
	{
		return $this->iCurrentIndex;
	}

	/**
		Move forward to next element.

		@return weeLDAPEntry An instance of weeLDAPEntry.
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function next()
	{
		$this->rEntry = ldap_next_entry($this->rLink, $this->rEntry);

		if ($this->rEntry === false)
			return false;

		$this->iCurrentIndex++;
	}

	/**
		Return the number of entries found in the current result.

		@return integer Number of the entries found in the current result.
		@throw LDAPException If an error occurs.
	*/

	public function numResults()
	{
		$iEntries = ldap_count_entries($this->rLink, $this->rResult);
		if ($iEntries === false)
			throw new LDAPException(
				_WT('weeLDAPResult::numResults failed to count entries.') . "\n" . ldap_error(),
				ldap_errno($this->rLink)
			);

		return $iEntries;
	}

	/**
		Rewind the Iterator to the first element.

		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function rewind()
	{
		$this->iCurrentIndex = 0;

		$this->rEntry = ldap_first_entry($this->rLink, $this->rResult);
		if ($this->rEntry === false)
			throw new LDAPException(
				_WT('Failed to get the first entry.') . "\n" . ldap_error(), 
				ldap_errno($this->rLink)
			);
	}

	/**
		Sort the entries found in the current result, by the specified filter.

		@throw LDAPException If an error occurs.
	*/

	public function sort($sFilter)
	{
		$b = ldap_sort($this->rLink, $this->rResult, $sFilter);
		if ($b === false)
			throw new LDAPException(
				_WT('weeLDAPResult::sort failed to sort entries.') . "\n" . ldap_error(), 
				ldap_errno($this->rLink)
			);
	}

	/**
		Check if there is a current element after calls to rewind() or next().

		@return bool whether there is a current element after calls to rewind() or next();
		@see http://www.php.net/~helly/php/ext/spl/interfaceIterator.html
	*/

	public function valid()
	{
		return $this->rEntry !== false;
	}
}
