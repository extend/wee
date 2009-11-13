<?php

$b42  = $oHelper->isSelected(42);  // not selected,  returns false
$b777 = $oHelper->isSelected(777); // doesn't exist, returns false
$b999 = $oHelper->isSelected(999); // selected,      returns true
