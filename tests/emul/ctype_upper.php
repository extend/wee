<?php

require_once('init.php');

return

// Valid

    ctype_upper('FELMNFKLFDSNFSKLFNSDL') && emul_ctype_upper('FELMNFKLFDSNFSKLFNSDL')

// Invalid

&& !ctype_upper('fjsdiopfhsiofnuios') && !emul_ctype_upper('fjsdiopfhsiofnuios')
&& !ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_upper('5686541641') && !emul_ctype_upper('5686541641')
&& !ctype_upper('5A1C9B3F') && !emul_ctype_upper('5A1C9B3F')
&& !ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_upper('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_upper('1.5') && !emul_ctype_upper('1.5')
&& !ctype_upper('?*#') && !emul_ctype_upper('?*#')
&& !ctype_upper("\r\n\t") && !emul_ctype_upper("\r\n\t")
&& !ctype_upper(' ') && !emul_ctype_upper(' ')
&& !ctype_upper('') && !emul_ctype_upper('')
&& !ctype_upper(null) && !emul_ctype_upper(null)

;

?>
