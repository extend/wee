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

class weeSearchPagination extends weePagination
{
	public function __toString()
	{
		$iCurrent	= intval($this->iFrom / $this->iPerPage);
		$this->getPagesFromTo($iCurrent, 10, $iFrom, $iTo);

		$s = '<div class="searchpagination"><p>' . _('Results page:') . '</p>';

		//TODO:new search

		if ($iCurrent != 0)
			$s .= '<a href="' . $this->sURI . 'from=' . (($iCurrent - 1) * $this->iPerPage) . '" class="previous">' . _('Previous') . ' &laquo;</a> ';
		else
			$s .= '<span class="previous">' . _('Previous') . ' &laquo;</span> ';

		$s .= $this->getPagesLinks($iCurrent, $iFrom, $iTo);

		if ($iCurrent + 1 < $iTo)
			$s .= '<a href="' . $this->sURI . 'from=' . (($iCurrent + 1) * $this->iPerPage) . '" class="next">&raquo; ' . _('Next') . '</a>';
		else
			$s .= '<span class="next">&raquo; ' . _('Next') . '</span>';

		return $s . '</div>';
	}
}

?>
