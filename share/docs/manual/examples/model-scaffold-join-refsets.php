<?php

class myUsersSet extends weeDbSetScaffold
{
	protected $sModel = 'myUsersModel';
	protected $sTableName = 'users';

	// Reference tables to fetch by doing a JOIN in SELECT queries.
	protected $aRefSets = array('myProfilesSet');
}
