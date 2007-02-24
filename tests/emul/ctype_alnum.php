<?php

require_once('init.php');

return

// Valid

    ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn') && emul_ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn')
&&  ctype_alnum('5686541641') && emul_ctype_alnum('5686541641')
&&  ctype_alnum('5A1C9B3F') && emul_ctype_alnum('5A1C9B3F')
&&  ctype_alnum('fjsdiopfhsiofnuios') && emul_ctype_alnum('fjsdiopfhsiofnuios')
&&  ctype_alnum('FELMNFKLFDSNFSKLFNSDL') && emul_ctype_alnum('FELMNFKLFDSNFSKLFNSDL')

// Invalid

&& !ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn?') && !emul_ctype_alnum('0123456789azertyuiopqsdfghjklmwxcvbn?')
&& !ctype_alnum('1.5') && !emul_ctype_alnum('1.5')
&& !ctype_alnum('?*#') && !emul_ctype_alnum('?*#')
&& !ctype_alnum("\r\n\t") && !emul_ctype_alnum("\r\n\t")
&& !ctype_alnum(' ') && !emul_ctype_alnum(' ')
&& !ctype_alnum('') && !emul_ctype_alnum('')
&& !ctype_alnum(null) && !emul_ctype_alnum(null)

;

?>
