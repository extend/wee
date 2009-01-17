<?php

class weeDataSource_test extends weeDataSource {
	public function mustEncodeData() {
		return $this->bMustEncodeData;
	}
}


$o = new weeDataSource_test;
$this->isFalse($o->mustEncodeData(),
	_WT('weeDataSource::bMustEncodeData should be false right after the object creation.'));

$o->encodeData();
$this->isTrue($o->mustEncodeData(),
	_WT('weeDataSource::bMustEncodeData should be true after a call to weeDataSource::encodeData.'));
