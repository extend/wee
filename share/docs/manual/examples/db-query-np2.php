<?php

// Note: It is highly recommended to filter the input first

$oResults = weeApp()->db->query('SELECT * FROM wee_articles WHERE art_id=:id', $_GET);
