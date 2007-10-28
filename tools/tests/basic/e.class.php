<?php

class e extends weeUnitTestCase
{
	protected $success;

	public function __construct()
	{
		$this->success = true;
	}

	public function run()
	{
		return $this->test();
	}

	protected function test()
	{
		return $this->success;
	}
}

?>
