<?php

$iStart			= 3;
$iTheAnswerIs	= (1 + $iStart++) * 9;
$bDoIt			= testAvailability() && !testReadiness();
