<?php

// Note: Always filter the data first

// Insert a random row
$oResults = weeApp()->db->query('INSERT INTO wee_articles (art_contents) VALUES (:art_contents)', $_POST);

// Retrieve the id and display it
echo weeApp()->db->getPKId();
