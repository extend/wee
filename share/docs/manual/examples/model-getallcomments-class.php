<?php

class myUsersModel extends weeModel
{
	// skipping other stuff ...

	public function getAllComments()
	{
		$oSet = new myCommentsSet;
		return $oSet->fetchAllForUser($this['user_id']);
	}
}
