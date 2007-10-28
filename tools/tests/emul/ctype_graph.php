<?php

require_once('init.php');

return

// Valid

    ctype_graph('fjsdiopfhsiofnuios') && emul_ctype_graph('fjsdiopfhsiofnuios')
&&  ctype_graph('FELMNFKLFDSNFSKLFNSDL') && emul_ctype_graph('FELMNFKLFDSNFSKLFNSDL')
&&  ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn') && emul_ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn')
&&  ctype_graph('5686541641') && emul_ctype_graph('5686541641')
&&  ctype_graph('5A1C9B3F') && emul_ctype_graph('5A1C9B3F')
&&  ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn?') && emul_ctype_graph('0123456789azertyuiopqsdfghjklmwxcvbn?')
&&  ctype_graph('1.5') && emul_ctype_graph('1.5')
&&  ctype_graph('?*#') && emul_ctype_graph('?*#')

// Invalid

&& !ctype_graph("\r\n\t") && !emul_ctype_graph("\r\n\t")
&& !ctype_graph(' ') && !emul_ctype_graph(' ')
&& !ctype_graph('') && !emul_ctype_graph('')
&& !ctype_graph(null) && !emul_ctype_graph(null)

;

?>
