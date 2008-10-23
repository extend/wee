<?php

$this->isEqual(
	'Time to say &apos;night.',
	xmlspecialchars("Time to say 'night."),
	_("Encoding of ' failed.")
);

$this->isEqual(
	'&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot;',
	xmlspecialchars('">_>" & "<_<"'),
	_('Encoding of ">_>" & "<_<" failed.')
);

$this->isEqual(
	'東方妖々夢',
	xmlspecialchars('東方妖々夢'),
	_('xmlspecialchars should not encode unicode characters.')
);
