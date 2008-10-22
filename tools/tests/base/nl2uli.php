<?php

$sList 			= 'flour' . "\r\n" . 'sugar' .  "\r\n" . 'water' . "\r\n" . 'eggs' . "\r\n" . 'salt, pepper' .  "\r\n";
$sExpectedList	= '<ul><li>flour</li><li>sugar</li><li>water</li><li>eggs</li><li>salt, pepper</li></ul>';
$sSimpleElement	= 'flour';

$sResult = nl2uli($sList);
$this->isEqual($sResult, $sExpectedList,
		sprintf(_('nl2uli should return "%s" got "%s" instead.'), $sExpectedList, $sResult));

$sResult = nl2uli($sSimpleElement);
$this->isEqual($sResult, '<ul><li>' . $sSimpleElement . '</li></ul>',
		sprintf(_('nl2uli should return "%s" got "%s" instead.'), '<ul><li>' . $sSimpleElement . '</li></ul>', $sResult));

$sResult = nl2uli('');
$this->isEqual($sResult, '',
		sprintf(_('nl2uli should return "" (empty) got "%s" instead.'), $sResult));

$sResult = nl2uli($sExpectedList);
$this->isEqual($sResult, '<ul><li>' . $sExpectedList . '</li></ul>',
		sprintf(_('nl2uli should return "%s" got "%s" instead.'), '<ul><li>' . $sExpectedList . '</li></ul>', $sResult));

