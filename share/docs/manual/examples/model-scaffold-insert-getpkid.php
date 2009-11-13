<?php

class myUsersSet extends weeDbSetScaffold
{
	// (skipping)...

	public function insert($aData)
	{
		$oModel = parent::insert($aData);
		$oModel['user_id'] = $this->getDb()->getPKId();
		return $oModel;
	}
}
