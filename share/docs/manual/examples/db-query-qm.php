<?php

$oResults = weeApp()->db->query('SELECT * FROM wee_articles WHERE art_id=? AND art_date=?', 3, '2008-02-15');
