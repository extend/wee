<?php

class myExampleModel extends weeDbModel
{
	public function getAllComments()
	{
		return myCommentsSet::instance()->fetchAllForUser($this['user_id']);
	}

	public function update()
	{
		$this->query('
			UPDATE table SET the_name=:the_name, the_email=:the_email
			WHERE the_id=:the_id
		', $this->aData);
	}
}

// Example use:
$oModel = myExampleSet::instance()->fetch(42);

$oAllComments = $oModel->getAllComments();

$oModel['the_name'] = 'Changed!';
$oModel->update();
