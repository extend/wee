<?php

class myExampleSet extends weeDbSet
{
	public function fetch($iId)
	{
		return $this->getDb()->query('
			SELECT * FROM table WHERE the_id=?
		', $iId)->rowClass('myExampleModel')->fetch();
	}

	public static function instance()
	{
		return new self;
	}
}

// Example use:

$oModel = myExampleSet::instance()->fetch(42);
