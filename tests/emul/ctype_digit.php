<?php

require_once('init.php');

return

// Valid

    ctype_digit('5686541641') && emul_ctype_digit('5686541641')

// Invalid

&& !ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_digit('fjsdiopfhsiofnuios') && !emul_ctype_digit('fjsdiopfhsiofnuios')
&& !ctype_digit('FELMNFKLFDSNFSKLFNSDL') && !emul_ctype_digit('FELMNFKLFDSNFSKLFNSDL')
&& !ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_digit('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_digit('5A1C9B3F') && !emul_ctype_digit('5A1C9B3F')
&& !ctype_digit('1.5') && !emul_ctype_digit('1.5')
&& !ctype_digit('?*#') && !emul_ctype_digit('?*#')
&& !ctype_digit("\r\n\t") && !emul_ctype_digit("\r\n\t")
&& !ctype_digit(' ') && !emul_ctype_digit(' ')
&& !ctype_digit('') && !emul_ctype_digit('')
&& !ctype_digit(null) && !emul_ctype_digit(null)

;

?>
