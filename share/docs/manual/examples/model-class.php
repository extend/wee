<?php

class myExampleModel extends weeDbModel
{
	public function getAllComments()
	{
		return myCommentsSet::instance()->fetchAllForUser($this['user_id']);
	}
}

// Example use:
$oModel = myExampleSet::instance()->fetch(42);

$oAllComments = $oModel->getAllComments();
