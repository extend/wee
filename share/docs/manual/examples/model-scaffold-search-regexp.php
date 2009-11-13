<?php

class myUsersSet extends weeDbSetScaffold
{
	protected $sModel = 'myUsersModel';
	protected $sTableName = 'users';

	public function __construct()
	{
		$this->aValidCriteriaOperators[] = 'REGEXP';
		$this->aValidCriteriaOperators[] = 'NOT REGEXP';
	}
}
