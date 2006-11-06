<?php

class basic_c
{
}

class basic_c_bad_bad_bad
{
}

$o = new basic_c;

return !($o instanceof basic_c_bad_bad_bad);

?>
