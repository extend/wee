<?php

// Set class for our profiles.

class myProfilesSet extends weeDbSetScaffold
{
	// Model associated with this set of elements.
	// This set will always return elements according to this model.
	protected $sModel = 'myProfilesModel';

	// Name of the table in the database represented by this set.
	protected $sTableName = 'profiles';
}

// Model class for our profiles.

class myProfilesModel extends weeDbModelScaffold
{
	// Name of the weeDbSetScaffold class associated with this model.
	protected $sSet = 'myProfilesSet';
}
