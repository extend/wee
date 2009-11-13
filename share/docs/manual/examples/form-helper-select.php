<?php

// Select an item
$oHelper->select(42);

// Select another item
$oHelper->select(3);

// Unselect all
$oHelper->selectNone();

// Select exactly one item
$oHelper->selectOne(2);

// Oops, I wanted to select 999, let's change it
$oHelper->selectOne(999);

// At this point, only 999 is selected
