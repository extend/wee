<?php

weeApp()->cookies['my_str'] = 'egg';
weeApp()->cookies['my_int'] = 42;

if (isset(weeApp()->cookies['my_str']))
	echo weeApp()->cookies['my_str'];
