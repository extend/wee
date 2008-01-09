<?php

$sFile = str_replace(
	array('<?php', '?>', "if (!function_exists('ctype_alnum'))", 'function ctype'),
	array('', '', '', 'function emul_ctype'),
	file_get_contents(WEE_PATH . 'emul_ctype' . PHP_EXT)
);

eval($sFile);
