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
	$this->fail(_WT('weeFsModel should throw an InvalidArgumentException because no filename was specified.'));
} catch (InvalidArgumentException $e) {}

$aData = array('filename' => $sFilename);
try {
	$o = new weeFsModel_testFsModel($aData);
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

$aData = array('filename' => $sFilename);
try {
	$o = new weeFsModel_testFsModel($aData);

	$o->save();
	chmod($aData['filename'], 0666);

	$this->isTrue($o->exists(), 
		sprintf(_WT("The file %s should exists."), $sFilename));

	$this->isTrue($o->isReadable(), 
		sprintf(_WT("The file %s should be readable."), $sFilename));

	$this->isTrue($o->isWritable(), 
		sprintf(_WT("The file %s should be writable."), $sFilename));

	if (!defined('WEE_ON_WINDOWS')) {
		$o->makeLink($sLinkFilename);
		$this->isTrue(is_link($sLinkFilename), 
			sprintf(_WT("The file %s should be a symbolic link."), $sLinkFilename));
		unlink($sLinkFilename);
	}

	$o->delete();
	$this->isFalse(file_exists($sFilename),
		sprintf(_WT("weeFsModel::delete() the file %s should be deleted."), $sFilename));

} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeFsModel should not throw an InvalidArgumentException because the filename exists and was specified'));
}

try {
	$o = new weeFsModel_testFsModel($aData);

	$this->isFalse($o->exists(), 
		sprintf(_WT("The file %s should not exists."), $sFilename));

	$this->isFalse($o->isReadable(), 
		sprintf(_WT("The file %s should not be readable."), $sFilename));

	$this->isFalse($o->isWritable(), 
		sprintf(_WT("The file %s should not be writable."), $sFilename));

	if (!defined('WEE_ON_WINDOWS'))
		try {
			$o->makeLink(ROOT_PATH . 'app/tmp/blah');
			$this->fail(_WT('weeFsModel::makeLink should throw an IllegalStateException when the file does not exist.'));
		} catch (IllegalStateException $e) {}
} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

try {
	$o = new weeFsModel_testFsModel($aData);

	$o->save();

	$o->moveTo($sNewFilename);
	$this->isFalse(file_exists($aData['filename']), sprintf(_WT("The file %s should not exists"), $sFilename));

	$o->delete();
	$this->isFalse(file_exists($sNewFilename), sprintf(_WT("The file %s should not exists"), $sNewFilename));
} catch (UnexpectedValueException $e) {
	$this->fail(sprintf(_WT('weeFsModel should not throw an UnexpectedValueException when trying to rename %s.'), $aData['filename']));
}

try {
	$o = new weeFsModel_testFsModel($aData);

	@$o->delete();
	$this->isFalse(file_exists($aData['filename']), sprintf(_WT("The file %s should not exists"), $sFilename));

	@$o->moveTo($sNewFilename);
	$this->fail(sprintf(_WT('weeFsModel should throw an UnexpectedValueException when trying to rename %s.'), $aData['filename']));
} catch (UnexpectedValueException $e) {}

try {
	$o = new weeFsModel_testFsModel($aData);
	$o->updateName();

	$this->isEqual('fsmodel.txt', $o->aData['name'],
		_WT('The filename is not the one expected.'));

	$this->isEqual('txt', $o->aData['extension'],
		_WT('The file extension is not the one expected.'));

} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

try {
	$aData['filename'] = 'file.with.dots.tar.gz';
	$o = new weeFsModel_testFsModel($aData);
	$o->updateName();

	$this->isEqual('file.with.dots.tar.gz', $o->aData['name'],
		_WT('The filename is not the one expected.'));

	$this->isEqual('gz', $o->aData['extension'],
		_WT('The file extension is not the one expected.'));

} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}

try {
	$aData['filename'] = 'file_without_extension_and_slash';
	$o = new weeFsModel_testFsModel($aData);
	$o->updateName();

	$this->isEqual('file_without_extension_and_slash', $o->aData['name'],
		_WT('The filename is not the one expected.'));

	$this->isEqual('', $o->aData['extension'],
		_WT('The file extension is not the one expected.'));

} catch (InvalidArgumentException $e) {
	$this->fail(_WT('weeFsModel should not throw an InvalidArgumentException because the filename was specified'));
}
