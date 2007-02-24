<?php

require_once('init.php');

return

// Valid

    ctype_xdigit('5686541641') && emul_ctype_xdigit('5686541641')
&&  ctype_xdigit('5A1C9B3F') && emul_ctype_xdigit('5A1C9B3F')

// Invalid

&& !ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_xdigit('fjsdiopfhsiofnuios') && !emul_ctype_xdigit('fjsdiopfhsiofnuios')
&& !ctype_xdigit('FELMNFKLFDSNFSKLFNSDL') && !emul_ctype_xdigit('FELMNFKLFDSNFSKLFNSDL')
&& !ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_xdigit('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_xdigit('1.5') && !emul_ctype_xdigit('1.5')
&& !ctype_xdigit('?*#') && !emul_ctype_xdigit('?*#')
&& !ctype_xdigit("\r\n\t") && !emul_ctype_xdigit("\r\n\t")
&& !ctype_xdigit(' ') && !emul_ctype_xdigit(' ')
&& !ctype_xdigit('') && !emul_ctype_xdigit('')
&& !ctype_xdigit(null) && !emul_ctype_xdigit(null)

;

?>
