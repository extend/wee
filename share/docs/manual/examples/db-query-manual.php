<?php

// Note: It is highly recommended to filter the input first

$sQuery = 'SELECT * FROM wee_articles WHERE art_id=' . weeApp()->db->escape($_GET['id']);
$oResults = weeApp()->db->query($sQuery);
