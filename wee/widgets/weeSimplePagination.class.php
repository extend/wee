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
	Simple pagination widget.
*/

class weeSimplePagination extends weePagination
{
	/**
		Returns the widget XHTML code.

		@return string XHTML for this widget.
	*/

	public function toString()
	{
		$iCurrent	= intval($this->iFrom / $this->iPerPage);
		$iLast		= intval($this->iTotal / $this->iPerPage);
		$this->getPagesFromTo($iCurrent, 10, $iFrom, $iTo);

		$s = '<div class="simplepagination"><span>' . _('Page:') . '</span> ';

		if ($iTo > 3)
		{
			if ($iCurrent == 0)	$s .= _('First') . ' ';
			else				$s .= '<a href="' . substr($this->sURI, 0, -1) . '">' . _('First') . '</a> ';
		}

		$s .= $this->getPagesLinks($iCurrent, $iFrom, $iTo);

		if ($iTo > 3)
		{
			if ($iCurrent >= $iLast)	$s .= _('Last');
			else						$s .= '<a href="' . $this->sURI . 'from=' . $this->iTotal . '">' . _('Last') . '</a>';
		}

		return $s . '</div>';
	}
}

?>
