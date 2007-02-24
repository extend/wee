<?php

require_once('init.php');

return

// Valid

    ctype_print('fjsdiopfhsiofnuios') && emul_ctype_print('fjsdiopfhsiofnuios')
&&  ctype_print('FELMNFKLFDSNFSKLFNSDL') && emul_ctype_print('FELMNFKLFDSNFSKLFNSDL')
&&  ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn') && emul_ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn')
&&  ctype_print('5686541641') && emul_ctype_print('5686541641')
&&  ctype_print('5A1C9B3F') && emul_ctype_print('5A1C9B3F')
&&  ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn?') && emul_ctype_print('0123456789azertyuiopqsdfghjklmwxcvbn?')
&&  ctype_print('1.5') && emul_ctype_print('1.5')
&&  ctype_print('?*#') && emul_ctype_print('?*#')
&&  ctype_print(' ') && emul_ctype_print(' ')

// Invalid

&& !ctype_print("\r\n\t") && !emul_ctype_print("\r\n\t")
&& !ctype_print('') && !emul_ctype_print('')
&& !ctype_print(null) && !emul_ctype_print(null)

;

?>
