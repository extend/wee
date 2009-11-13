<?php

class test
{
	public function curly($iBrace)
	{
		if ($iBrace > 0) {
			echo '>0!';
			return 0;
		} else {
			echo '<=0!';
			somethingHappened('<=0');
			return 1;
		}
	}
}
