<?php

$oDb = new weePgSQLDatabase(array(
	'host'		=> 'localhost',
	'user'		=> 'wee_tests',
	'password'	=> 'wee_tests',
	'dbname'	=> 'wee_tests',
));

$oDb->query('
	CREATE TABLE prepstat (
		ps_serial SERIAL NOT NULL,
		ps_integer INTEGER,
		ps_varchar CHARACTER VARYING(50),
		ps_timestamp TIMESTAMP WITHOUT TIME ZONE,
		ps_boolean BOOLEAN
	);
');

$aInsertValues = array(
	array(
		'ps_integer'	=> 42,
		'ps_varchar'	=> "The world's most advanced open source database.",
		'ps_timestamp'	=> '2004-10-19 10:23:54',
		'ps_boolean'	=> 'FALSE',
	),
	array(
		'ps_integer'	=> 4242,
		'ps_varchar'	=> "The universe's most advanced open source database.",
		'ps_timestamp'	=> '2005-10-19 15:59:00',
		'ps_boolean'	=> 'TRUE',
	),
	array(
		'ps_integer'	=> 424242,
		'ps_varchar'	=> "The sea's most advanced open source database.",
		'ps_timestamp'	=> '2006-10-19 03:01:47',
		'ps_boolean'	=> 'FALSE',
	),
	array(
		'ps_integer'	=> -1000,
		'ps_varchar'	=> "The cheese's most advanced open source database.",
		'ps_timestamp'	=> '2007-10-19 20:21:22',
		'ps_boolean'	=> 'FALSE',
	),
);

$oInsertStat = $oDb->prepare('
	INSERT INTO prepstat (ps_integer, ps_varchar, ps_timestamp, ps_boolean)
	VALUES (:ps_integer, :ps_varchar, :ps_timestamp, :ps_boolean)'
);

foreach ($aInsertValues as $aRow)
	$oInsertStat->bind($aRow)->execute();

$oSelectStat = $oDb->prepare('
	SELECT * FROM prepstat
');

$oResults = $oSelectStat->execute();
$iNumResults = count($oResults);

$aImplicitTest = $oDb->prepare('
	SELECT ps_integer FROM prepstat WHERE ps_boolean=:0
')->bind(array('TRUE'))->execute()->fetch();

$oDb->query('DROP TABLE prepstat');

unset($oDb);

return $iNumResults == 4 && $aImplicitTest['ps_integer'] == 4242;

?>
