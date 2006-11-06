<?php

return	weeNumberValidator::test(0) &&
		weeNumberValidator::test(1) &&
		weeNumberValidator::test(-1) &&
		!weeNumberValidator::test('32f');

?>
