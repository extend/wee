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
	Base class for action controllers.

	Child classes must at least overload the $aConfig property.

	There are three types of methods used in this class:
		* checkCommit*
		* commitAction*
		* doAction*
	Each * can be replaced by add, upd, del, or any other actions you may want to handle.

	The commitAction* and doAction* methods must exist for each action, but the checkCommit* is optional.
	It will by default only check if the form is correctly submitted.
*/

abstract class weeActionController
{
	/**
		Configuration array.

		It can contains the following elements:
			* table		The table where data will be add/upd/del.
			* pkey		The primary key of the table.
			* add_form	The add form.
			* add_tpl	The add template.
			* del_form	The del form.
			* del_tpl	The del template.
			* upd_form	The upd form.
			* upd_tpl	The upd template.
	*/

	protected $aConfig;

	/**
		Data submitted.
		May be an empty array if no data was submitted.
	*/

	protected $aData;

	/**
		The database object used to add/upd/del.
	*/

	protected $oDatabase;

	/**
		Initialize the controller by providing him the database object and data.

		@param $oDatabase	The database object used to add/upd/del.
		@param $aData		Data submitted. May be an empty array if no data was submitted.
	*/

	public function __construct($oDatabase, array $aData)
	{
		fire(empty($this->aConfig['table']));

		$this->oDatabase	= $oDatabase;
		$this->aData		= $aData;
	}

	/**
		Default check before commiting.
		It just checks if the form is valid.

		@param	$oForm	The form for this action.
		@param	$oTpl	The template for this action.
		@return	bool	Whether the commit is valid.
	*/

	protected function checkCommit($oForm, $oTpl)
	{
		$bHasErrors = $oForm->hasErrors($this->aData);

		if ($bHasErrors)
			$oTpl->set('errors', $oForm->getErrors());

		return !$bHasErrors;
	}

	/**
		If data isn't empty, check if it is valid and commit.

		@param	$sAction	Action being done.
		@param	$oForm		The form for this action.
		@param	$oTpl		The template for this action.
	*/

	protected function commitAction($sAction, $oForm, $oTpl)
	{
		$sMethod = 'checkCommit' . $sAction;
		if (!method_exists($this, $sMethod))
			$sMethod = 'checkCommit';

		if ($this->$sMethod($oForm, $oTpl))
		{
			$sMethod = 'commitAction' . $sAction;
			$this->$sMethod($oForm, $oTpl);
		}

		$oForm->fill($this->aData);
	}

	/**
		Insert a new row.

		@param	$oForm	The form for this action.
		@param	$oTpl	The template for this action.
	*/

	protected function commitActionAdd($oForm, $oTpl)
	{
		$this->oDatabase->query($oForm->toSQL($this->aData, $this->aConfig['table']));
		fire($this->oDatabase->numAffectedRows() == 0, 'DatabaseException');
	}

	/**
		Delete the given row.

		@param	$oForm	The form for this action.
		@param	$oTpl	The template for this action.
	*/

	protected function commitActionDel($oForm, $oTpl)
	{
		if ($this->aData['confirm_delete'])
		{
			$this->oDatabase->query('
				DELETE
					FROM ' . $this->aConfig['table'] . '
					WHERE ' . $this->aConfig['pkey'] . '=?
					LIMIT 1
			', $this->aData[$this->aConfig['pkey']]);
		}
	}

	/**
		Update an existing row.

		@param	$oForm	The form for this action.
		@param	$oTpl	The template for this action.
	*/

	protected function commitActionUpd($oForm, $oTpl)
	{
		$this->oDatabase->query(
			$oForm->toSQL(
				$this->aData,
				$this->aConfig['table']
			)->where(§(
				eq,
				'`' . $this->aConfig['pkey'] . '`',
				$this->aData[$this->aConfig['pkey']]
			))
		);
	}

	/**
		Convenience function for creating atom feeds in one line.

		@return weeActionController A new weeActionController object.
	*/

	public static function create($oDatabase, array $aData)
	{
		return new self($oDatabase, $aData);
	}

	/**
		Do the specified action.

		@param $sAction Action being done.
		@param $sPKeyId Primary key value for the row targeted.
	*/

	public function doAction($sAction, $sPKeyId = null)
	{
		fire(empty($sAction));

		$sMethod = 'doAction' . $sAction;
		$this->$sMethod($sPKeyId);
	}

	/**
		Do the add action.

		@return weeTemplate The template for this action.
	*/

	protected function doActionAdd()
	{
		fire(empty($this->aConfig['add_form']));
		fire(empty($this->aConfig['add_tpl']));

		$oForm	= new weeForm($this->aConfig['add_form'], weeForm::ACTION_ADD);
		$oTpl	= new weeTemplate($this->aConfig['add_tpl'], array(
			'action'		=> weeForm::ACTION_ADD,
			'form'			=> $oForm,
			'is_submitted'	=> !empty($this->aData),
		));

		if (!empty($this->aData))
			$this->commitAction('add', $oForm, $oTpl);

		return $oTpl;
	}

	/**
		Do the del action.

		@param	$sPKeyId	Primary key value for the row targeted.
		@return	weeTemplate	The template for this action.
	*/

	protected function doActionDel($sPKeyId)
	{
		fire(empty($this->aConfig['pkey']));
		fire(empty($this->aConfig['del_form']));
		fire(empty($this->aConfig['del_tpl']));

		$oForm	= new weeForm($this->aConfig['del_form'], weeForm::ACTION_DEL);
		$oTpl	= new weeTemplate($this->aConfig['del_tpl'], array(
			'action'		=> weeForm::ACTION_UPD,
			'form'			=> $oForm,
			'is_submitted'	=> !empty($this->aData),
		));

		$oForm->widget($this->aConfig['pkey'])->setValue($sPKeyId);

		if (!empty($this->aData))
			$this->commitAction('del', $oForm, $oTpl);

		return $oTpl;
	}

	/**
		Do the upd action.

		@param	$sPKeyId	Primary key value for the row targeted.
		@return	weeTemplate	The template for this action.
	*/

	protected function doActionUpd($sPKeyId)
	{
		fire(empty($this->aConfig['pkey']));
		fire(empty($this->aConfig['upd_form']));
		fire(empty($this->aConfig['upd_tpl']));

		$oForm	= new weeForm($this->aConfig['upd_form'], weeForm::ACTION_UPD);
		$oTpl	= new weeTemplate($this->aConfig['upd_tpl'], array(
			'action'		=> weeForm::ACTION_UPD,
			'form'			=> $oForm,
			'is_submitted'	=> !empty($this->aData),
		));

		$oForm->fill($this->getCurrent($sPKeyId));

		if (!empty($this->aData))
			$this->commitAction('upd', $oForm, $oTpl);

		return $oTpl;
	}

	/**
		Return data for the given primary key.

		@param	$sPKeyId	Primary key value for the row targeted.
		@return	array		The row for the given primary key.
	*/

	protected function getCurrent($sPKeyId)
	{
		fire(empty($aConfig['pkey']));

		return $this->oDatabase->query('
			SELECT *
				FROM ' . $this->aConfig['table'] . '
				WHERE ' . $this->aConfig['pkey'] . '=?
				LIMIT 1
		', $sPKeyId)->fetch();
	}
}

?>
