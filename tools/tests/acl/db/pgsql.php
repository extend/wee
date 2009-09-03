<?php

require(ROOT_PATH . 'tools/tests/db/pgsql/connect.php.inc');

$oDb->query('
	CREATE TABLE roles
	(
	   role_id serial NOT NULL, 
	   perm_operation character varying(8), 
	   perm_resource character varying(8), 
	   perm_id1 integer, 
	   perm_id2 integer, 
	   CONSTRAINT pk_roles PRIMARY KEY (role_id) 
	) WITH (OIDS=FALSE);
');

$oDb->query('
	CREATE TABLE user_role
	(
	   user_id integer NOT NULL, 
	   role_id integer NOT NULL, 
	   CONSTRAINT pk_user_role PRIMARY KEY (user_id, role_id), 
	   CONSTRAINT fk_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (role_id)    ON UPDATE NO ACTION ON DELETE CASCADE
	) WITH (OIDS=FALSE);
');

try {

	$oACL = new weeACLDbTable(array(
		'db' => $oDb,
		'sr_table' => 'user_role',
		'rp_table' => 'roles',
		'resource_fields' => array('perm_resource', 'perm_id1', 'perm_id2'),
	));

	$oACL->add(42, null, null);
	$oACL->add(462, 'write', array(
		'perm_resource' => 'not much',
		'perm_id1' => 4,
		'perm_id2' => 62,
	));
	$oACL->add(2009, null, array('perm_resource' => 'hell'));
	$oACL->add(2009, 'read', null);
	$oACL->add(3456, 'read', null); // role should exist and not be re-created

	$a = $oACL->fetch(1);
	$this->isEqual(0, count($a),
		_WT('weeACLDbTable::fetch returned an incorrect number of roles for subject 1.'));

	$a = $oACL->fetch(42);
	$this->isEqual(1, count($a),
		_WT('weeACLDbTable::fetch returned an incorrect number of roles for subject 42.'));
	$this->isEqual(array(array('user_id' => 42, 'role_id' => 1, 'perm_operation' => null, 'perm_resource' => null, 'perm_id1' => null, 'perm_id2' => null)), $a,
		_WT('weeACLDbTable::fetch returned unexpected ACL values for subject 42.'));

	$a = $oACL->fetch(2009);
	$this->isEqual(2, count($a),
		_WT('weeACLDbTable::fetch returned an incorrect number of roles for subject 2009.'));
	$this->isEqual(array(array('user_id' => 2009, 'role_id' => 3, 'perm_operation' => null, 'perm_resource' => 'hell', 'perm_id1' => null, 'perm_id2' => null),
						 array('user_id' => 2009, 'role_id' => 4, 'perm_operation' => 'read', 'perm_resource' => null, 'perm_id1' => null, 'perm_id2' => null)), $a,
		_WT('weeACLDbTable::fetch returned unexpected ACL values for subject 2009.'));

	$a = $oACL->fetch(3456);
	$this->isEqual(1, count($a),
		_WT('weeACLDbTable::fetch returned an incorrect number of roles for subject 3456.'));
	$this->isEqual(array(array('user_id' => 3456, 'role_id' => 4, 'perm_operation' => 'read', 'perm_resource' => null, 'perm_id1' => null, 'perm_id2' => null)), $a,
		_WT('weeACLDbTable::fetch returned unexpected ACL values for subject 3456.'));

	// Testing the admin subjects (42)

	$this->isTrue($oACL->isAllowed(42),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, 'read'),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, 'win'),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42.'));
	$this->isTrue($oACL->isAllowed(42, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed  should always return true for subject 42.'));

	// Testing the restricted subjects (462, 2009, 3456)

	$this->isFalse($oACL->isAllowed(462),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 462.'));
	$this->isFalse($oACL->isAllowed(462, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 462.'));
	$this->isFalse($oACL->isAllowed(462, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 462.'));
	$this->isTrue($oACL->isAllowed(462, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 462.'));
	$this->isFalse($oACL->isAllowed(462, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 462.'));
	$this->isFalse($oACL->isAllowed(462, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 462.'));
	$this->isFalse($oACL->isAllowed(462, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 462.'));
	$this->isFalse($oACL->isAllowed(462, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 462.'));

	$this->isFalse($oACL->isAllowed(2009),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 2009.'));
	$this->isTrue($oACL->isAllowed(2009, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 2009.'));
	$this->isFalse($oACL->isAllowed(2009, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 2009.'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 2009.'));
	$this->isFalse($oACL->isAllowed(2009, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 2009.'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 2009.'));
	$this->isTrue($oACL->isAllowed(2009, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 2009.'));
	$this->isTrue($oACL->isAllowed(2009, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 2009.'));

	$this->isFalse($oACL->isAllowed(3456),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 3456.'));
	$this->isTrue($oACL->isAllowed(3456, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 3456.'));
	$this->isFalse($oACL->isAllowed(3456, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 3456.'));
	$this->isFalse($oACL->isAllowed(3456, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 3456.'));
	$this->isFalse($oACL->isAllowed(3456, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 3456.'));
	$this->isFalse($oACL->isAllowed(3456, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 3456.'));
	$this->isFalse($oACL->isAllowed(3456, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 3456.'));
	$this->isTrue($oACL->isAllowed(3456, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 3456.'));

	// Deleting rights

	$oACL->delete(2009, 'read', null);

	$this->isFalse($oACL->isAllowed(2009),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 2009 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 2009 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 2009 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 2009 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 2009 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 2009 after deleting (2009,read,null).'));
	$this->isTrue($oACL->isAllowed(2009, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 2009 after deleting (2009,read,null).'));
	$this->isTrue($oACL->isAllowed(2009, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 2009 after deleting (2009,read,null).'));

	$this->isFalse($oACL->isAllowed(3456),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 3456 after deleting (2009,read,null).'));
	$this->isTrue($oACL->isAllowed(3456, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 3456 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 3456 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 3456 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 3456 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 3456 after deleting (2009,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 3456 after deleting (2009,read,null).'));
	$this->isTrue($oACL->isAllowed(3456, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 3456 after deleting (2009,read,null).'));

	$oACL->delete(3456, 'read');

	$this->isFalse($oACL->isAllowed(2009),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 2009 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 2009 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 2009 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 2009 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 2009 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 2009 after deleting (3456,read,null).'));
	$this->isTrue($oACL->isAllowed(2009, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 2009 after deleting (3456,read,null).'));
	$this->isTrue($oACL->isAllowed(2009, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 2009 after deleting (3456,read,null).'));

	$this->isFalse($oACL->isAllowed(3456),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 3456 after deleting (3456,read,null).'));
	$this->isFalse($oACL->isAllowed(3456, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 3456 after deleting (3456,read,null).'));

	$oACL->delete(42);

	$this->isFalse($oACL->isAllowed(42),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, 'read'),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, 'win'),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed should always return true for subject 42 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(42, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed  should always return true for subject 42 after deleting (42,null,null).'));

	$this->isFalse($oACL->isAllowed(2009),
		_WT('weeACLDbTable::isAllowed failed the test #1 of subject 2009 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'read'),
		_WT('weeACLDbTable::isAllowed failed the test #2 of subject 2009 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'win'),
		_WT('weeACLDbTable::isAllowed failed the test #3 of subject 2009 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #4 of subject 2009 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(2009, null, array('perm_resource' => 'not much', 'perm_id1' => 4, 'perm_id2' => 62)),
		_WT('weeACLDbTable::isAllowed failed the test #5 of subject 2009 after deleting (42,null,null).'));
	$this->isFalse($oACL->isAllowed(2009, 'write', array('perm_resource' => 'not much', 'perm_id1' => 5, 'perm_id2' => 63)),
		_WT('weeACLDbTable::isAllowed failed the test #6 of subject 2009 after deleting (42,null,null).'));
	$this->isTrue($oACL->isAllowed(2009, null, array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #7 of subject 2009 after deleting (42,null,null).'));
	$this->isTrue($oACL->isAllowed(2009, 'read', array('perm_resource' => 'hell')),
		_WT('weeACLDbTable::isAllowed failed the test #8 of subject 2009 after deleting (42,null,null).'));

} catch (Exception $eException) {}

$oDb->query('DROP TABLE ' . $oDb->escapeIdent('user_role'));
$oDb->query('DROP TABLE ' . $oDb->escapeIdent('roles'));
if (isset($eException))
	throw $eException;
