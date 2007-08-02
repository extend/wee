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

/**
	Base class for feed generation.

	TODO:function to set feed/entry language? Another way to do that?
*/

abstract class weeFeed implements Printable
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
		@overload link(TODO:verif url$sURL)			Sets the link to the corresponding entry.
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
		fire(empty($sName) || !ctype_alpha(str_replace(':', '', $sName)), 'InvalidParameterException');
		fire(!$this->isElementValid($sName), 'InvalidParameterException');
		fire(sizeof($aArgs) != 1, 'InvalidParameterException');

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
			- author
			- category
			- link
			- summary
			- title
			- updated
			TODO:id

		//TODO:validates each entry elements

		@param	$aEntry The entry data.
		@return	$this
	*/

	public function entry($aEntry)
	{
		fire(empty($aEntry));

		foreach ($aEntry as $sElement => $m)
			fire(!$this->isEntryElementValid($sElement), 'InvalidArgumentException');

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
			'id',
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
			'id',
			'link',
			'published',
			'summary',
			'title',
			'updated',
		);

		return in_array($sElement, $aValidElements);
	}
}

?>
