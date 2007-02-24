<?php

require_once('init.php');

return

// Valid

    ctype_alpha('fjsdiopfhsiofnuios') && emul_ctype_alpha('fjsdiopfhsiofnuios')
&&  ctype_alpha('FELMNFKLFDSNFSKLFNSDL') && emul_ctype_alpha('FELMNFKLFDSNFSKLFNSDL')

// Invalid

&& !ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_alpha('5686541641') && !emul_ctype_alpha('5686541641')
&& !ctype_alpha('5A1C9B3F') && !emul_ctype_alpha('5A1C9B3F')
&& !ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_alpha('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_alpha('1.5') && !emul_ctype_alpha('1.5')
&& !ctype_alpha('?*#') && !emul_ctype_alpha('?*#')
&& !ctype_alpha("\r\n\t") && !emul_ctype_alpha("\r\n\t")
&& !ctype_alpha(' ') && !emul_ctype_alpha(' ')
&& !ctype_alpha('') && !emul_ctype_alpha('')
&& !ctype_alpha(null) && !emul_ctype_alpha(null)

;

?>
