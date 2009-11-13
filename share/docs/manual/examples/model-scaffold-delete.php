<?php

// Delete user #42
$oSet->delete(42);

// Delete user #42 from the year 2009
$oSet->delete(array('user_id' => 42, 'user_year' => 2009));
