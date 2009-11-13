<?php

$oResults = weeApp()->db->query('SELECT * FROM articles ORDER BY art_date DESC LIMIT 5');
