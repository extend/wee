<?php

$sDirname	= ROOT_PATH . 'app/tmp/fsdirectorymodel';
$sDirname2	= ROOT_PATH . 'app/tmp/fsdirectorymodel/tmp2';
$sDirname3	= ROOT_PATH . 'app/tmp/fsdirectorymodel/tmp2/tmp3';
$sFilename	= $sDirname . '/file.txt';
$sFilename2	= $sDirname2 . '/file.txt';
$sFilename3	= $sDirname3 . '/file.txt';
$aData 		= array ('filename' => $sDirname);

$iRet = @mkdir($sDirname, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create (or recreate) the directory %s.'), $sDirname));

$iRet = @mkdir($sDirname2, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create the directory %s.'), $sDirname2));

$iRet = @mkdir($sDirname3, 0755);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create the directory %s.'), $sDirname3));

touch($sFilename);
touch($sFilename2);
touch($sFilename3);

chmod($sDirname, 0400);

try {
	$o = new weeFsDirectoryModel($aData);

	$o->deleteContents();
	$this->isTrue(file_exists($sFilename), 
		sprintf(_('weeFsDirectoryModel::deleteContents(), the contents in %s should not be deleted.'), $sDirname));

	$this->fail(sprintf(_('weeFsDirectoryModel should throw an NotPermittedException when trying to delete the contents of %'), $sDirname));
} catch (NotPermittedException $e) {}

try {
	$o = new weeFsDirectoryModel($aData);

	$o->delete();
	$this->isTrue(file_exists($sDirname),
		sprintf(_('weeFsDirectoryModel::delete(), the directory %s should not be deleted.'), $sDirname));

	$this->fail(sprintf(_('weeFsDirectoryModel should throw an NotPermittedException when trying to delete %'), $sDirname));
} catch (NotPermittedException $e) {}

exec(sprintf('chmod -R 755 %s', $sDirname));

try {
	$o = new weeFsDirectoryModel($aData);

	$o->deleteContents();
	$this->isFalse(file_exists($sFilename), 
		sprintf(_('weeFsDirectoryModel::deleteContents(), the contents in %s should be deleted.'), $sDirname));
	$this->isFalse(file_exists($sFilename2),
		sprintf(_('weeFsDirectoryModel::deleteContents(), the contents in %s should be deleted.'), $sDirname2));
	$this->isFalse(file_exists($sFilename3),
		sprintf(_('weeFsDirectoryModel::deleteContents(), the contents in %s should be deleted.'), $sDirname3));

	$o->delete();
	$this->isFalse(file_exists($sDirname),
		sprintf(_('weeFsDirectoryModel::deleteContents(), the directory %s should be deleted.'), $sDirname));

} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsDirectoryModel should not throw an InvalidArgumentException because the Filename was specified'));
}
