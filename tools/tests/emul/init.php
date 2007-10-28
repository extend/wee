<?php

$sFile = str_replace(
	array('<?php', '?>', "if (!extension_loaded('ctype'))", 'function ctype'),
	array('', '', '', 'function emul_ctype'),
	file_get_contents(WEE_PATH . 'emul_ctype' . PHP_EXT)
);

eval($sFile);

return null;

?>
