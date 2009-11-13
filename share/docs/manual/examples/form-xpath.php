<?php

// Retrieve all the widgets
$aResults = $oForm->xpath('widget');

// Retrieve all textarea widgets and change their label
$aResults = $oForm->xpath('widget[@type="textarea"]');

foreach ($aResults as $oNode)
	$oNode->label = 'xpathed';
