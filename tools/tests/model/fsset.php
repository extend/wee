<?php

$sDirname 		= ROOT_PATH . 'app/tmp/fsset';
$sFilename 		= ROOT_PATH . 'app/tmp/fsset/file.txt';
$sFilename2		= ROOT_PATH . 'app/tmp/fsset/file2.txt';
$sLinkFilename 	= ROOT_PATH . 'app/tmp/fsset/linkfile.txt';

$iRet = mkdir($sDirname);
$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the directory %s.'), $sDirname));

if (!defined('WEE_ON_WINDOWS')) {
	$iRet = symlink($sFilename, $sLinkFilename);
	$iRet === false and burn('UnexpectedValueException', sprintf(_WT('Cannot create the symbolic link %s.'), $sLinkFilename));
}

touch($sFilename);
touch($sFilename2);

$o = new weeFsSet;
$oModel = $o->fetch($sDirname);
$this->isInstanceof($oModel, 'weeFsDirectoryModel',
		sprintf(_WT('weeFsSet::fetch should return a weeFsDirectoryModel instance, got a %s instance instead.'), get_class($oModel)));

if (!defined('WEE_ON_WINDOWS')) {
	$oModel = $o->fetch($sLinkFilename);
	$this->isInstanceof($oModel, 'weeFsLinkModel',
			sprintf(_WT('weeFsSet::fetch should return a weeFsDirectoryModel instance, got a %s instance instead.'), get_class($oModel)));
}

$oModel = $o->fetch($sFilename);
$this->isInstanceof($oModel, 'weeFsFileModel',
		sprintf(_WT('weeFsSet::fetch should return a weeFsFileModel instance, got a %s instance instead.'), get_class($oModel)));

$aFiles = $o->fetchPath($sDirname);
$this->isFalse(empty($aFiles), _WT('weeFsSet::fetchPath should not return an empty array.'));

unlink($sFilename);
unlink($sFilename2);
if (!defined('WEE_ON_WINDOWS'))
	unlink($sLinkFilename);

$aFiles = $o->fetchPath($sDirname);
$this->isTrue(empty($aFiles), _WT('weeFsSet::fetchPath should return an empty array.'));
