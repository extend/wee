<?php

require_once('init.php');

return

// Valid

    ctype_lower('fjsdiopfhsiofnuios') && emul_ctype_lower('fjsdiopfhsiofnuios')

// Invalid

&& !ctype_lower('FELMNFKLFDSNFSKLFNSDL') && !emul_ctype_lower('FELMNFKLFDSNFSKLFNSDL')
&& !ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_lower('5686541641') && !emul_ctype_lower('5686541641')
&& !ctype_lower('5A1C9B3F') && !emul_ctype_lower('5A1C9B3F')
&& !ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_lower('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_lower('1.5') && !emul_ctype_lower('1.5')
&& !ctype_lower('?*#') && !emul_ctype_lower('?*#')
&& !ctype_lower("\r\n\t") && !emul_ctype_lower("\r\n\t")
&& !ctype_lower(' ') && !emul_ctype_lower(' ')
&& !ctype_lower('') && !emul_ctype_lower('')
&& !ctype_lower(null) && !emul_ctype_lower(null)

;

?>
