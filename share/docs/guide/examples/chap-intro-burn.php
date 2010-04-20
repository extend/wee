<?php

// Instead of this...
throw new ExampleException('Oops!');

// Do this!
burn('ExampleException', 'Oops!');

// You can also use it this way:
$r = fopen('/path/to/file', 'r')
	or burn('FileNotFoundException', 'The file /path/to/file does not exist.');
