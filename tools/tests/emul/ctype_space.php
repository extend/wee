<?php

require_once('init.php');

return

// Valid


    ctype_space("\r\n\t") && emul_ctype_space("\r\n\t")
&&  ctype_space(' ') && emul_ctype_space(' ')

// Invalid

&& !ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_space('5686541641') && !emul_ctype_space('5686541641')
&& !ctype_space('5A1C9B3F') && !emul_ctype_space('5A1C9B3F')
&& !ctype_space('fjsdiopfhsiofnuios') && !emul_ctype_space('fjsdiopfhsiofnuios')
&& !ctype_space('FELMNFKLFDSNFSKLFNSDL') && !emul_ctype_space('FELMNFKLFDSNFSKLFNSDL')
&& !ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_space('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_space('1.5') && !emul_ctype_space('1.5')
&& !ctype_space('?*#') && !emul_ctype_space('?*#')
&& !ctype_space('') && !emul_ctype_space('')
&& !ctype_space(null) && !emul_ctype_space(null)

;

?>
