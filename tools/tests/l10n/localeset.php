<?php

if (!function_exists('locale_set_default'))
	return $this->skip();

$o = new weeLocale;

$sBefore = setlocale(LC_ALL, 'en_US');

$o->set('C');
$this->isEqual('C', setlocale(LC_ALL, 0),
	_WT('The locale was no correctly set by weeLocale::set.'));

$o->set('en', 'UTF-8');
$this->isEqual('en_US.UTF-8', setlocale(LC_MESSAGES, 0),
	_WT('The locale was no correctly set by weeLocale::set.'));

setlocale(LC_ALL, $sBefore);
