<?php

class myExampleSet extends weeDbSet
{
	protected $sModel = 'myExampleModel';

	public function fetch($iId)
	{
		return $this->getDb()->query('
			SELECT * FROM table WHERE the_id=?
		', $iId)->rowClass('myExampleModel')->fetch();
	}

	public function fetchAll()
	{
		return $this->query('
			SELECT * FROM table
		');
	}

	public static function instance()
	{
		return new self;
	}
}

// Example use:

$oModel = myExampleSet::instance()->fetch(42);
$oResults = myExampleSet::instance()->fetchAll();
