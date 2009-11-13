<?php

// Create a default output if none has been created before
$oOutput = new weeXHTMLOutput;

// Change output used
weeOutput::select(new weeTextOutput);
