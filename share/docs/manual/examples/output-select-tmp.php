<?php

// Switch output driver
$oOldOutput = weeOutput::select(new weeTextOutput);
// Do your thing
doSomething();
// Switch back to the previous driver
weeOutput::select($oOldOutput);
