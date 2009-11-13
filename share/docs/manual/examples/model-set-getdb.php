<?php

class myExampleSet extends weeDbSet
{
	public function fetch($iId)
	{
		return $this->getDb()->query('
			SELECT * FROM table WHERE the_id=?
		', $iId)->rowClass('myExampleModel')->fetch();
	}
}

// Example use:

$oSet = new myExampleSet;
$oModel = $oSet->fetch(42);
