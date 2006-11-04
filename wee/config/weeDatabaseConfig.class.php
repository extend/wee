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

class weeDatabaseConfig extends weeConfig
{
	protected $oDatabase;
	protected $sTable;

	public function __construct($oDatabase, $sTable)
	{
		$this->oDatabase	= $oDatabase;
		$this->sTable		= $sTable;

		$oQuery = $this->oDatabase->Query('SELECT * FROM ' . $this->sTable);
		foreach ($oQuery as $a)
			$this->aConfig[$a['name']] = $a['value'];
	}

	public function offsetSet($offset, $value)
	{
		parent::offsetSet($offset, $value);
		$this->oDatabase->Query('UPDATE ' . $this->sTable . ' SET value=? WHERE name=? LIMIT 1', $value, $offset);
	}

	public function offsetUnset($offset)
	{
		parent::offsetUnset($offset);
		$this->oDatabase->Query('DELETE FROM ' . $this->sTable . ' WHERE name=? LIMIT 1', $offset);
	}
}

?>
