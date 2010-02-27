<?php

/**
	Simple pastebin example.

	This set contains all the pastebins in the database.
	It can be used to fetch existing pastebins but also insert or delete them.

	We just define the table used and make sure we get the last inserted id when returning the inserted model.
*/

class pastebinSet extends weeDbSetScaffold
{
	protected $sTableName = 'pastebin';
	protected $sModel = 'pastebinModel';

	public function insert($aData)
	{
		$oModel = parent::insert($aData);
		$oModel['pastebin_id'] = $this->getDb()->getPKId();
		return $oModel;
	}
}

/**
	The model is the actual pastebin object.
	It is holding the pastebin data and can be used to modify it.
*/

class pastebinModel extends weeDbModelScaffold
{
	protected $sSet = 'pastebinSet';
}
