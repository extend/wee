<?php

/*
	Web:Extend
	Copyright (c) 2006-2008 Dev:Extend

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
	Scaffolding for database elements.

	To use it, simply extend it and define the $sSet property to the name of the weeDbSetScaffold class.
*/

abstract class weeDbModelScaffold extends weeDbModel
{
	/**
		Name of the weeDbSetScaffold class associated with this model.
	*/

	protected $sSet;

	/**
		Creates a new instance of this model with the data passed as parameter.
		Also gets the metadata information from the set.

		@param $aData Data to be set at initialization.
	*/

	public function __construct($aData)
	{

		parent::__construct($aData);
	}

	/**
		Saves the data stored in this model to the database.

		@throw IllegalStateException The data was empty or the table has no primary key.
	*/

	public function update()
	{
		static $aMeta;

		if (empty($aMeta)) {
			$oSet = new $this->sSet;
			$aMeta = $oSet->getMeta();
		}

		empty($this->aData) and burn('IllegalStateException', _WT('The model do not contain any data.'));
		empty($aMeta['primary']) and burn('IllegalStateException', _WT('The table has no primary key defined.'));

		$oDb = $this->getDb();
		$sQuery = 'UPDATE ' . $aMeta['table'] . ' SET ';

		foreach ($this->aData as $sName => $mValue)
			if (in_array($sName, $aMeta['columns']) && !in_array($sName, $aMeta['primary']))
				$sQuery .= $oDb->escapeIdent($sName) . '=' . $oDb->escape($this->aData[$sName]) . ', ';

		$sQuery = substr($sQuery, 0, -2) . ' WHERE TRUE';
		foreach ($aMeta['primary'] as $sName)
			$sQuery .= ' AND ' . $oDb->escapeIdent($sName) . '=' . $oDb->escape($this->aData[$sName]);

		$this->query($sQuery);
	}
}
