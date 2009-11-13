<?php

$oResults = $oDb->query('SELECT * FROM users LIMIT 100 OFFSET ?', array_value($aEvent['get'], 'from', 0));

$oPagination = new weePaginationUI;
$oPagination->setParams(array(
	'countperpage'	=> 100,
	'total'			=> count($oResults),
));
$this->addFrame('pagination', $oPagination);
