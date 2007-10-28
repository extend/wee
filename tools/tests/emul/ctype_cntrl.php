<?php

require_once('init.php');

return

// Valid

    ctype_cntrl("\r\n\t") && emul_ctype_cntrl("\r\n\t")

// Invalid

&& !ctype_cntrl('fjsdiopfhsiofnuios') && !emul_ctype_cntrl('fjsdiopfhsiofnuios')
&& !ctype_cntrl('FELMNFKLFDSNFSKLFNSDL') && !emul_ctype_cntrl('FELMNFKLFDSNFSKLFNSDL')
&& !ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_cntrl('5686541641') && !emul_ctype_cntrl('5686541641')
&& !ctype_cntrl('5A1C9B3F') && !emul_ctype_cntrl('5A1C9B3F')
&& !ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_cntrl('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_cntrl('1.5') && !emul_ctype_cntrl('1.5')
&& !ctype_cntrl('?*#') && !emul_ctype_cntrl('?*#')
&& !ctype_cntrl(' ') && !emul_ctype_cntrl(' ')
&& !ctype_cntrl('') && !emul_ctype_cntrl('')
&& !ctype_cntrl(null) && !emul_ctype_cntrl(null)

;

?>
