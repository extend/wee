<?php

require('connect.php.inc');

try {
	$oDb->query('
		CREATE GLOBAL TEMPORARY TABLE GETPKID (
			PK_ID INTEGER PRIMARY KEY,
			PK_VALUE INTEGER
		)
	');

	$oDb->query('
		CREATE TRIGGER GETPKID_TRIGGER
		BEFORE INSERT ON GETPKID FOR EACH ROW
		BEGIN
			IF (:NEW.PK_ID IS NULL) THEN
				SELECT GETPKID_SEQ.NEXTVAL INTO :NEW.PK_ID FROM DUAL;
			END IF;
		END;
	');

	$oDb->query('CREATE SEQUENCE GETPKID_SEQ');
} catch (DatabaseException $e) {
	// This is expected to fail if the temporary table already exist
}

$mPK = 'GETPKID_SEQ';
require(dirname(__FILE__) . '/../getpkid.php.inc');
