<?php

class basic_c
{
}

class basic_c_bad_bad_bad
{
}

$o = new basic_c;

$this->isNotInstanceOf($o, 'basic_c_bad_bad_bad',
	'$o instanceof basic_c_bad_bad_bad?');
