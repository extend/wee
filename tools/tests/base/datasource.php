<?php

class weeDataSource_test extends weeDataSource {}

$o = new weeDataSource_test;
$this->isNull($o->getEncoder(),
	_WT('weeDataSource::getEncoder should return null right after the object creation.'));

$oEncoder = new weeTextEncoder;
$o->setEncoder($oEncoder);
$this->isEqual($oEncoder, $o->getEncoder(),
	_WT('weeDataSource::getEncoder should be return the encoder given to weeDataSource::encodeData.'));
