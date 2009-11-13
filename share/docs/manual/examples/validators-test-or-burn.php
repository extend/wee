<?php

weeStringValidator::test($s, array('min' => 1))
	or burn('UnexpectedValueException', 'The given string is empty.');
