<?php

$sFilename 		= ROOT_PATH . 'app/tmp/fslinkmodel.txt';
$sLinkFilename 	= ROOT_PATH . 'app/tmp/fslinkmodellinkfile.txt';
$aData 			= array('filename' => $sLinkFilename);

touch($sFilename);

$iRet = symlink($sFilename, $sLinkFilename);
$iRet === false and burn('UnexpectedValueException', sprintf(_('Cannot create symbolic link %s.'), $sLinkFilename));

try {
	$o = new weeFsLinkModel($aData);
	$oModel = $o->getTarget();
	$this->isInstanceof($oModel, 'weeFsFileModel',
		sprintf(_('weeFsLinkModel::getTarget should return a weeFsFileModel instance, got a %s instance instead'), get_class($oModel)));
} catch (UnexpectedValueException $e) {
	$this->fail(_('weeFsLinkModel should not throw an UnexpectedValueException.'));
}
