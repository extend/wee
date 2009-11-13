<?php

if ($oMessage->numAttachments() > 0) {
	$aAttachments = $oMessage->getAttachments;

	foreach ($aAttachments as $oAttach)
		$oAttach->saveTo('/tmp');
}
