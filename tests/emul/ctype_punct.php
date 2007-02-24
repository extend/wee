<?php

require_once('init.php');

return

// Valid


   ctype_punct('?*#') && emul_ctype_punct('?*#')

// Invalid

&& !ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn') && !emul_ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn')
&& !ctype_punct('5686541641') && !emul_ctype_punct('5686541641')
&& !ctype_punct('5A1C9B3F') && !emul_ctype_punct('5A1C9B3F')
&& !ctype_punct('fjsdiopfhsiofnuios') && !emul_ctype_punct('fjsdiopfhsiofnuios')
&& !ctype_punct('FELMNFKLFDSNFSKLFNSDL') && !emul_ctype_punct('FELMNFKLFDSNFSKLFNSDL')
&& !ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_punct('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_punct('1.5') && !emul_ctype_punct('1.5')
&& !ctype_punct("\r\n\t") && !emul_ctype_punct("\r\n\t")
&& !ctype_punct(' ') && !emul_ctype_punct(' ')
&& !ctype_punct('') && !emul_ctype_punct('')
&& !ctype_punct(null) && !emul_ctype_punct(null)

;

?>
