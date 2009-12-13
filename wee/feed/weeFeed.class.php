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
	Base class for feed generation.

	TODO:function to set feed/entry language? Another way to do that?
*/

abstract class weeFeed implements weeRenderer
{
	/**
		Contains the feed-related data.
	*/

	protected $aFeed	= array();

	/**
		Contains the entry-related data.
		Each element of this array is an entry.
	*/

	protected $aEntries	= array();

	/**
		A few elements are required for each feed.
		The following overloaded functions are defined for all the feed classes.

		@overload author($aAuthor)		Sets the feed author informations. The array can(TODO) have the following keys: name, email and TODO:uri.
		@overload category($sCategory)	Sets the category for all entries.
		@overload link($sURL)			Sets the link to the corresponding entry. TODO:check url
		@overload logo($sIRI TODO:IRI)	Sets the logo location.
		@overload rights($sCopyright)	Sets the feed copyright.
		@overload subtitle($sSubTitle)	Sets a small descriptive subtitle.
		@overload title($sTitle)		Sets the feed title.
		@overload updated($sDate)		Sets the feed build date. TODO:format
		TODO:id
		TODO:generator?
	*/

	public function __call($sName, $aArgs)
	{
		(empty($sName) || !ctype_alpha(str_replace(':', '', $sName))) and burn('InvalidArgumentException',
			_WT('$sName must be defined and contain only alpha characters or a colon.'));
		$this->isElementValid($sName) or burn('BadMethodCallException', sprintf(_WT('"%s" is not a valid feed element name.'), $sName));
		sizeof($aArgs) == 1 or burn('InvalidArgumentException', sprintf(_WT('The method "%s" accepts only one argument.'), $sName));

		$this->aFeed[$sName] = $aArgs[0];

		return $this;
	}

	/**
		Adds multiple entries.

		@param	$aEntries The entries to add.
		@return	$this
	*/

	public function entries($aEntries)
	{
		foreach ($aEntries as $aEntry)
			$this->entry($aEntry);

		return $this;
	}

	/**
		Adds an entry.

		Each entry can have one of the following keys:
			* author
			* category
			* link
			* summary
			* title
			* updated
			* TODO:id

		//TODO:validates each entry elements

		@param	$aEntry The entry data.
		@return	$this
	*/

	public function entry($aEntry)
	{
		empty($aEntry) and burn('UnexpectedValueException', _WT('$aEntry must not be empty.'));

		foreach ($aEntry as $sElement => $m)
			$this->isEntryElementValid($sElement) or burn('InvalidArgumentException',
				sprintf(_WT('"%s" is not a valid feed entry element name.'), $sElement));

		$this->aEntries[] = $aEntry;

		return $this;
	}

	/**
		Checks if given feed-related element is valid.

		@param	$sElement	The element name.
		@return	bool		True if the element is valid, false otherwise.
	*/

	protected function isElementValid($sElement)
	{
		$aValidElements = array(
			'author',
			'category',
			'link',
			'logo',
			'rights',
			'self',
			'subtitle',
			'title',
			'updated',
		);

		return in_array($sElement, $aValidElements);
	}

	/**
		Checks if given entry-related element is valid.

		@param	$sElement	The element name.
		@return	bool		True if the element is valid, false otherwise.
	*/

	protected function isEntryElementValid($sElement)
	{
		$aValidElements = array(
			'author',
			'category',
			'link',
			'published',
			'summary',
			'title',
			'updated',
		);

		return in_array($sElement, $aValidElements);
	}

	/**
		Output the feed.
	*/

	public function render()
	{
		echo $this->toString();
	}
}
