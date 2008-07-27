<?php

$this->isEqual(
	xmlspecialchars("Time to say 'night."),
	'Time to say &apos;night.',
	"Encoding of ' failed."
);

$this->isEqual(
	xmlspecialchars('">_>" & "<_<"'),
	'&quot;&gt;_&gt;&quot; &amp; &quot;&lt;_&lt;&quot;',
	'Encoding of ">_>" & "<_<" failed.'
);

$this->isEqual(
	xmlspecialchars('東方妖々夢'),
	'東方妖々夢',
	'xmlspecialchars should not encode unicode characters.'
);
