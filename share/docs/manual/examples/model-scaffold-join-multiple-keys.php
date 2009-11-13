<?php

class myUsersSet extends weeDbSetScaffold
{
	// (skipping)...

	// Reference tables to fetch by doing a JOIN in SELECT queries.
	protected $aRefSets = array(array('set' => 'myProfilesSet', 'key' => array('profile_id' => 'id', 'user_gender' => 'gender')));
}
