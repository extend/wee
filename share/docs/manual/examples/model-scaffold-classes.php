<?php

// Model class for our users.

class myUsersModel extends weeDbModelScaffold
{
	// Name of the weeDbSetScaffold class associated with this model.
	protected $sSet = 'myUsersSet';
}

// Set class for our users.

class myUsersSet extends weeDbSetScaffold
{
	// Model associated with this set of elements.
	// This set will always return elements according to this model.
	protected $sModel = 'myUsersModel';

	// Name of the table in the database represented by this set.
	protected $sTableName = 'users';
}
