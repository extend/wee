<?php

weeApp()->session['my_str'] = 'egg';
weeApp()->session['my_int'] = 42;

echo weeApp()->session['my_str'];
