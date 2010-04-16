<?php

/*
	Web:Extend Extra Library
	Copyright (c) 2006-2010 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
	List all files in a directory and store various informations about them in the resulting array.

	@param $sPath Path to the directory to list.
	@param $aMimeIcons List of icons for each known mime types.
	@return array The verbose list of files.
	@todo links pointing to nothing fail
	@todo handle empty $aMimeIcons properly
*/

function fs_list_directory_contents($sPath, $aMimeIcons = array()) {
	try {
		$oDirectory = new DirectoryIterator($sPath);
	} catch (Exception $e) {
		// The folder doesn't exist, so there is no files to list.
		return array();
	}

	$oFileInfo = new finfo(FILEINFO_MIME);
	$aFiles = array();

	foreach ($oDirectory as $oFile) {
		if ($oFile->isDot())
			continue;

		$sFilename = $oFile->getFilename();
		$sExt = strrchr($sFilename, '.');
		$iSize = $oFile->getSize();
		$sMimeType = $oFileInfo->file($oFile->getRealPath());
		$iMTime = $oFile->getMTime();

		// Folder is preferred
		if ($sMimeType == 'directory')
			$sMimeType = 'folder';

		$aFiles[$sFilename] = array(
			'icon'		=> ui_mime_to_icon($sMimeType, $aMimeIcons),
			'filename'	=> $sFilename,
			'ext'		=> $sExt === false ? null : substr($sExt, 1),
			'size'		=> $iSize,
			'husize'	=> human_size($iSize),
			'mimetype'	=> $sMimeType,
			'mtime'		=> $iMTime,
			'humtime'	=> human_date($iMTime),
		);
	}

	ksort($aFiles);
	return $aFiles;
}

/**
	Validate a form. Automatically fill data and errors when the validation fails.

	@param $oForm The form to validate.
	@param $aData Data received. When returning, only the data available from the form itself will remain in the array.
	@return bool Whether the form is valid.
*/

function form_validate($oForm, &$aData) {
	$aData = $oForm->filter($aData);

	try {
		$oForm->validate($aData);
		return true;
	} catch (FormValidationException $e) {
		$oForm->fill($aData);
		$oForm->fillErrors($e);
		return false;
	}
}

/**
	Return a human-readable date based on the unix time given.

	@param $iTime Unix time to convert.
	@return string Human-readable date.
*/

function human_date($iTime) {
	$iToday = time();
	$iYesterday = $iToday - 86400;
	$sFormat = _WT('m/d/Y');

	$sDate = date($sFormat, $iTime);

	if ($sDate == date($sFormat, $iToday))
		return _WT('Today');
	if ($sDate == date($sFormat, $iYesterday))
		return _WT('Yesterday');

	return $sDate;
}

/**
	Return a human-readable size based on the size given in bytes.

	@param $iSize The size, in bytes.
	@return string Human-readable size.
*/

function human_size($iSize) {
	$aUnits = explode(',', _WT('B,kB,MB,GB,TB,PB'));
	$iCount = count($aUnits) - 1;

	for ($i = 0; $i < $iCount && $iSize > 1024; $i++)
		$iSize /= 1024;

	return round($iSize, 1) . ' ' . $aUnits[$i];
}

/**
	List data in a scaffold set. Meant for use in a frame.

	@param $oSet The scaffold set.
	@param $aEvent The frame's event.
	@param $aColumns Restrict the list to those columns.
	@return array The set's data to be displayed.
*/

function set_data_list($oSet, $aEvent, $aColumns = null) {
	$aMeta = $oSet->getMeta();

	if ($aColumns === null)
		$aColumns = $aMeta['columns'];

	if (!empty($aEvent['get']['sort']))
		$oSet->orderBy(array($aEvent['get']['sort'] => array_value($aEvent['get'], 'order', 'asc')));

	$aData = $oSet->fetchSubset((int)array_value($aEvent['get'], 'from', 0), (int)array_value($aEvent['get'], 'max', 0))->fetchAll();
	foreach ($aData as $i => $a) {
		$aPKey = array();
		foreach ($aMeta['primary'] as $sKey)
			$aPKey[$sKey] = $a[$sKey];

		$aData[$i]['row_actions'] = array(
			array('label' => _WT('Update'), 'url' => new weeURL(APP_PATH . $aEvent['frame'] . '/update', $aPKey)),
			array('label' => _WT('Delete'), 'url' => new weeURL(APP_PATH . $aEvent['frame'] . '/delete'), 'method' => 'post', 'data' => $aPKey),
		);
	}

	return array(
		'actions' => array(
			array('label' => _WT('Add'), 'url' => new weeURL(APP_PATH . $aEvent['frame'] . '/add')),
		),
		'columns' => $aColumns,
		'data' => $aData,
		'order' => array_value($aEvent['get'], 'order'),
		'sort' => array_value($aEvent['get'], 'sort'),
		'url' => new weeURL(APP_PATH . $aEvent['frame'], $aEvent['get']),
	);
}

/**
	Return the correct icon name for a given mime type.

	@param $sMimeType The mime type.
	@param $aMimeIcons The icons available.
	@return The icon name for this mime type.
*/

function ui_mime_to_icon($sMimeType, $aMimeIcons) {
	$sIconName = str_replace('/', '-', $sMimeType);

	// If it matches an icon name, then no need to search further

	if (in_array($sIconName, $aMimeIcons))
		return $sIconName;

	// Special case for folders

	if ($sMimeType == 'folder' || $sMimeType == 'directory')
		return 'folder';

	// No special cases so far for all audio-*, font-*, image-* and video-* types

	if (strncmp($sMimeType, 'audio/', 6) == 0)
		return 'audio-x-generic';
	if (strncmp($sMimeType, 'font/',  5) == 0)
		return 'font-x-generic';
	if (strncmp($sMimeType, 'image/', 6) == 0)
		return 'image-x-generic';
	if (strncmp($sMimeType, 'video/', 6) == 0)
		return 'video-x-generic';

	// Other known types

	static $aKnownTypes = array(
		'application/ogg'	=> 'audio-x-generic',

		'application/octet-stream'	=> 'package-x-generic',
		'application/x-archive'		=> 'package-x-generic',
		'application/x-bzip2'		=> 'package-x-generic',
		'application/x-gzip'		=> 'package-x-generic',
		'application/x-rar'			=> 'package-x-generic',
		'application/x-tar'			=> 'package-x-generic',
		'application/zip'			=> 'package-x-generic',

		'application/xml'	=> 'text-html',

		// Skipping text/plain as we return text-x-generic by default
		'application/pgp-keys'	=> 'text-x-generic',

		'text/x-c'				=> 'text-x-script',
		'text/x-c++'			=> 'text-x-script',
		'text/x-java'			=> 'text-x-script',
		'text/x-pascal'			=> 'text-x-script',
		'text/x-perl'			=> 'text-x-script',
		'text/x-php'			=> 'text-x-script',
		'text/x-shellscript'	=> 'text-x-script',

		'application/msword'						=> 'x-office-document',
		'application/pdf'							=> 'x-office-document',
		'application/vnd.ms-office'					=> 'x-office-document',
		'application/vnd.oasis.opendocument.text'	=> 'x-office-document',
		'text/rtf'									=> 'x-office-document',

		// The following ones couldn't be tested so far, they're seen as x-office-document or text-plain
		'application/vnd.oasis.opendocument.graphics'	=> 'x-office-drawing',

		'application/vnd.ms-powerpoint'						=> 'x-office-presentation',
		'application/vnd.oasis.opendocument.presentation'	=> 'x-office-presentation',

		'application/vnd.ms-excel'							=> 'x-office-spreadsheet',
		'application/vnd.oasis.opendocument.spreadsheet'	=> 'x-office-spreadsheet',
		'text/csv'											=> 'x-office-spreadsheet',
	);

	if (isset($aKnownTypes[$sMimeType]))
		return $aKnownTypes[$sMimeType];

	// Unknown type

	return 'text-x-generic';
}

/**
	Create a simple upload form and handle everything until the file is uploaded.

	@param $sDest Destination folder for the file.
	@param $oRenderer Template where the form is shown.
	@param $oForm Custom form in case the default one isn't enough.
*/

function upload_form($sDest, $oRenderer, $oForm = null) {
	if ($oForm === null)
		$oForm = new weeForm('weexlib/upload');

	if (!empty($_FILES)) {
		try {
			upload_move_to($sDest);
			$oRenderer['upload_success'] = true;
		} catch (Exception $e) {
			$oForm->fillErrors(array('file' => $e->getMessage()));
		}
	}

	$oRenderer['upload_form'] = $oForm;
}

/**
	Move the uploaded file to the destination folder.

	@param $sDest Destination folder for the file.
	@param $sName Name of the uploaded file.
*/

function upload_move_to($sDest, $sName = 'file') {
	$oUploads = new weeUploads;
	$oFile = $oUploads->fetch($sName);

	$oFile->isOK() or burn('UnexpectedValueException',
		_WT(sprintf('Upload of the file %s failed with the following error: %s',
			$oFile->getFilename(), $oFile->getError())));

	$oFile->moveTo($sDest);
}

/**
	Move all the uploaded files to the destination folder.

	@param $sDest Destination folder for the files.
*/

function upload_move_all_to($sDest) {
	$oUploads = new weeUploads;

	foreach ($oUploads as $oFile) {
		$oFile->isOK() or burn('UnexpectedValueException',
			_WT(sprintf('Upload of the file %s failed with the following error: %s',
				$oFile->getFilename(), $oFile->getError())));

		$oFile->moveTo($sDest);
	}
}
