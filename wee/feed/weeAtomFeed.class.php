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
	TODO
*/

class weeAtomFeed extends weeFeed
{
	//TODO:encode?
	public function __toString()
	{
		$sFeed = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n" . '<feed xmlns="http://www.w3.org/2005/Atom">';

		foreach ($this->aFeed as $sName => $mValue)
			$sFeed .= $this->elementToString($sName, $mValue);

		foreach ($this->aEntries as $aEntry)
		{
			$sFeed .= '<entry>';

			foreach ($aEntry as $sName => $mValue)
				$sFeed .= $this->elementToString($sName, $mValue);

			$sFeed .= '</entry>';
		}

		return $sFeed . '</feed>';
	}

	/**
		Convenience function for creating atom feeds in one line.
	*/

	public static function create()
	{
		return new weeAtomFeed;
	}

	protected function elementToString($sName, $mValue)
	{
		if ($sName == 'link')
			return '<link rel="alternate" href="' . htmlentities($mValue, ENT_COMPAT, 'utf-8') . '"/>';
		elseif ($sName == 'self')
			return '<link rel="self" href="' . $mValue . '"/>';
		elseif ($sName == 'category')
			return '<category term="' . $mValue . '"/>';
		else
		{
			if (($sName == 'published' || $sName == 'updated') && ctype_digit($mValue))
				$mValue = @date('c', $mValue);

			$sFeed = '<' . $sName;
			if ($sName == 'summary') //TODO:better
				$sFeed .= ' type="xhtml"';
			$sFeed .= '>';

			if (!is_array($mValue))
				$sFeed .= $mValue;
			else
			{
				foreach ($mValue as $sSubName => $sSubValue)
					$sFeed .= '<' . $sSubName . '>' . $sSubValue . '</' . $sSubName . '>';
			}

			return $sFeed . '</' . $sName . '>';
		}
	}
}

?>
