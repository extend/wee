<?php

class weeFsModel_testFsModel extends weeFsModel
{
	// Wee need it, wee expose it 
	public $aData;

	public function updateName()
	{
		return parent::updateName();
	}
}

$sFilename 		= ROOT_PATH . 'app/tmp/fsmodel.txt';
$sNewFilename 	= ROOT_PATH . 'app/tmp/fsmodelnew.txt';
$sLinkFilename 	= ROOT_PATH . 'app/tmp/fsmodellink.txt';

$aData = array();
try {
	$o = new weeFsModel_testFsModel($aData);
	$this->fail(_('weeFsModel should throw an InvalidArgumentException because no filename was specified.'));
} catch (InvalidArgumentException $e) {}

$aData = array('filename' => $sFilename);
try {
	$o = new weeFsModel_testFsModel($aData);
} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

$aData = array('filename' => $sFilename);
try {
	$o = new weeFsModel_testFsModel($aData);

	$o->save();
	chmod($aData['filename'], 0666);

	$this->isTrue($o->exists(), 
		sprintf(_("The file %s should exists."), $sFilename));

	$this->isTrue($o->isReadable(), 
		sprintf(_("The file %s should be readable."), $sFilename));

	$this->isTrue($o->isWritable(), 
		sprintf(_("The file %s should be writable."), $sFilename));

	if (!defined('WEE_ON_WINDOWS')) {
		$o->makeLink($sLinkFilename);
		$this->isTrue(is_link($sLinkFilename), 
			sprintf(_("The file %s should be a symbolic link."), $sLinkFilename));
		unlink($sLinkFilename);
	}

	$o->delete();
	$this->isFalse(file_exists($sFilename),
		sprintf(_("weeFsModel::delete() the file %s should be deleted."), $sFilename));

} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsModel should not throw an InvalidArgumentException because the filename exists and was specified'));
}

try {
	$o = new weeFsModel_testFsModel($aData);

	$this->isFalse($o->exists(), 
		sprintf(_("The file %s should not exists."), $sFilename));

	$this->isFalse($o->isReadable(), 
		sprintf(_("The file %s should not be readable."), $sFilename));

	$this->isFalse($o->isWritable(), 
		sprintf(_("The file %s should not be writable."), $sFilename));

	if (!defined('WEE_ON_WINDOWS')) {
		$o->makeLink('');
		$this->isFalse(is_link(''), _('The symbolic link "" (empty) should not exists'));
	}

} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

try {
	$o = new weeFsModel_testFsModel($aData);

	$o->save();

	$o->moveTo($sNewFilename);
	$this->isFalse(file_exists($aData['filename']), sprintf(_("The file %s should not exists"), $sFilename));

	$o->delete();
	$this->isFalse(file_exists($sNewFilename), sprintf(_("The file %s should not exists"), $sNewFilename));
} catch (UnexpectedValueException $e) {
	$this->fail(sprintf(_('weeFsModel should not throw an UnexpectedValueException when trying to rename %s.'), $aData['filename']));
}

try {
	$o = new weeFsModel_testFsModel($aData);

	@$o->delete();
	$this->isFalse(file_exists($aData['filename']), sprintf(_("The file %s should not exists"), $sFilename));

	@$o->moveTo($sNewFilename);
	$this->fail(sprintf(_('weeFsModel should throw an UnexpectedValueException when trying to rename %s.'), $aData['filename']));
} catch (UnexpectedValueException $e) {}

try {
	$o = new weeFsModel_testFsModel($aData);
	$o->updateName();

	$this->isEqual($o->aData['name'], 'fsmodel.txt',
		sprintf(_('The filename should be "fsmodel.txt" got "%s" instead'), $o->aData['name']));

	$this->isEqual($o->aData['extension'], 'txt',
		sprintf(_('The file extension should be "txt" got "%s" instead'), $o->aData['extension']));

} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

try {
	$aData['filename'] = 'file.with.dots.tar.gz';
	$o = new weeFsModel_testFsModel($aData);
	$o->updateName();

	$this->isEqual($o->aData['name'], 'file.with.dots.tar.gz',
		sprintf(_('The filename should be "file.with.dots.tar.gz" got "%s" instead'), $o->aData['name']));

	$this->isEqual($o->aData['extension'], 'gz',
		sprintf(_('The file extension should be "gz" got "%s" instead'), $o->aData['extension']));

} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

try {
	$aData['filename'] = 'file_without_extension_and_slash';
	$o = new weeFsModel_testFsModel($aData);
	$o->updateName();

	$this->isEqual($o->aData['name'], 'file_without_extension_and_slash',
		sprintf(_('The filename should be "file_without_extension_and_slash" got "%s" instead'), $o->aData['name']));

	$this->isEqual($o->aData['extension'], '',
		sprintf(_('The file extension should be empty got "%s" instead'), $o->aData['extension']));

} catch (InvalidArgumentException $e) {
	$this->fail(_('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}
