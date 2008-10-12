<?

$sDirname 		= ROOT_PATH . 'app/tmp/fsset';
$sFilename 		= ROOT_PATH . 'app/tmp/fsset/file.txt';
$sFilename2		= ROOT_PATH . 'app/tmp/fsset/file2.txt';
$sLinkFilename 	= ROOT_PATH . 'app/tmp/fsset/linkfile.txt';

$iRet = mkdir($sDirname);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create the directory %s.'), $sDirname));

$iRet = symlink($sFilename, $sLinkFilename);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create the symbolic link %s.'), $sLinkFilename));

touch($sFilename);
touch($sFilename2);

$o = new weeFsSet;
$oModel = $o->fetch($sDirname);
$this->isInstanceof($oModel, 'weeFsDirectoryModel',
		sprintf(_('weeFsSet::fetch should return a weeFsDirectoryModel instance, got a %s instance instead.'), get_class($oModel)));

$oModel = $o->fetch($sLinkFilename);
$this->isInstanceof($oModel, 'weeFsLinkModel',
		sprintf(_('weeFsSet::fetch should return a weeFsDirectoryModel instance, got a %s instance instead.'), get_class($oModel)));

$oModel = $o->fetch($sFilename);
$this->isInstanceof($oModel, 'weeFsFileModel',
		sprintf(_('weeFsSet::fetch should return a weeFsFileModel instance, got a %s instance instead.'), get_class($oModel)));

$aFiles = $o->fetchPath($sDirname);
$this->isFalse(empty($aFiles), _('weeFsSet::fetchPath should not return an empty array.'));

unlink($sFilename);
unlink($sFilename2);
unlink($sLinkFilename);

$aFiles = $o->fetchPath($sDirname);
$this->isTrue(empty($aFiles), _('weeFsSet::fetchPath should return an empty array.'));
