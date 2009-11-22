<?php

class myUsersSet extends weeDbSetScaffold
{
	protected $sModel = 'myUsersModel';
	protected $sTableName = 'users';

	public function __construct($aSubsetCriteria = array())
	{
		$this->aValidCriteriaOperators[] = 'REGEXP';
		$this->aValidCriteriaOperators[] = 'NOT REGEXP';

		// Don't forget to call the parent constructor
		// You are recommended to call it after setting additional criteria
		parent::__construct($aSubsetCriteria);
	}
}
