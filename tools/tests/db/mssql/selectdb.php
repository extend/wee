<?php

require('connect.php.inc');
$sCurrentDb = $oDb->queryValue('SELECT DB_NAME()');
require(dirname(__FILE__) . '/../selectdb.php.inc');
