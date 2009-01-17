<?php

require(dirname(__FILE__) . '/../../mysql/connect.php.inc');
$oMeta = $oDb->meta();

try
{
	$oDb->query('CREATE TABLE IF NOT EXISTS test1 (a integer, b integer, PRIMARY KEY (b, a)) ENGINE InnoDB');
	$oDb->query('CREATE TABLE IF NOT EXISTS test2 (a integer, b integer, c integer, CONSTRAINT i_am_a_fk FOREIGN KEY (c, b) REFERENCES test1 (b, a)) ENGINE InnoDB');

	$oTable = $oMeta->table('test2');

	// weeMySQLDbMetaTable::foreignKeys

	$aForeignKeysNames = array();
	foreach ($oTable->foreignKeys() as $oForeignKey)
		$aForeignKeysNames[] = $oForeignKey->name();

	$this->isEqual(array('i_am_a_fk'), $aForeignKeysNames,
		_WT('weeMySQLDbMetaTable::foreignKeys did not return the expected foreign key.'));

	// weeMySQLDbMetaTable::foreignKeyExists

	$this->isTrue($oTable->foreignKeyExists('i_am_a_fk'),
		_WT('weeMySQLDbMetaTable::foreignKeyExists should return true when the given foreign key exists in the table.'));

	$this->isFalse($oTable->foreignKeyExists('key_which_does_not_exist'),
		_WT('weeMySQLDbMetaTable::hasPrimaryKey should return false when the table does not have a primary key.'));

	// weeMySQLDbMetaTable::foreignKey

	try {
		$oTable->foreignKey('key_which_does_not_exist');
		$this->fail(_WT('weeMySQLDbMetaTable::foreignKey should throw an UnexpectedValueException when the given foreign key does not exist.'));
	} catch (UnexpectedValueException $e) {}

	$oForeignKey = $oTable->foreignKey('i_am_a_fk');

	// weeMySQLDbMetaForeignKey::name

	$this->isEqual('i_am_a_fk', $oForeignKey->name(),
		_WT('weeMySQLDbMetaForeignKey::name does not correctly return the name of the foreign key.'));

	// weeMySQLDbMetaForeignKey::columnsNames

	$this->isEqual(array('c', 'b'), $oForeignKey->columnsNames(),
		_WT('weeMySQLDbMetaForeignKey::columnsNames does not correctly return all the columns of the foreign key.'));

	// weeMySQLDbMetaForeignKey::referencedColumnsNames

	$this->isEqual(array('b', 'a'), $oForeignKey->referencedColumnsNames(),
		_WT('weeMySQLDbMetaForeignKey::referencedColumnsNames does not correctly return all the referenced columns of the foreign key.'));

	// weeMySQLDbMetaForeignKey::referencedTableName

	$this->isEqual('test1', $oForeignKey->referencedTableName(),
		_WT('weeMySQLDbMetaForeignKey::referencedTableName does not correctly return the name of the referenced table.'));
}
catch (Exception $oException) {}

$oDb->query('DROP TABLE IF EXISTS test2');
$oDb->query('DROP TABLE IF EXISTS test1');
if (isset($oException))
	throw $oException;
