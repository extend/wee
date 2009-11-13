<?php

$oResults = weeApp()->db->query('SELECT * FROM wee_articles WHERE art_id=:id', array('id' => 3));
