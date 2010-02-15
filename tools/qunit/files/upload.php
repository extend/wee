<?php

define('ALLOW_INCLUSION', 1);
define('DEBUG', 1);
define('ROOT_PATH', '../../../');
require(ROOT_PATH . 'wee/wee.php');
$iStep = array_value($_GET, 'step', 1);

function_exists('curl_init') or burn('ConfigurationException',
	'The cURL PHP extension is required to run this test.');

try {
	$o = new weeUploads;
	$sURL = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&upload=1';

	if ($iStep == 1) {
		if ($o->isEmpty()) {
			empty($_GET['upload']) or burn('IllegalStateException',
				_WT('No file was uploaded. Stopping here in order to prevent recursive execution.'));

			$r = curl_init();

			curl_setopt($r, CURLOPT_URL, $sURL);
			curl_setopt($r, CURLOPT_POST, true);
			curl_setopt($r, CURLOPT_POSTFIELDS, array('test' => '@' . dirname(__FILE__) . '/test.txt'));

			curl_exec($r) or burn('UnitTestException',
				sprintf(_WT('Upload failed: %s'), curl_error($r)));

			curl_close($r);
			exit;
		} else {
			$o->exists('test') or burn('UnitTestException',
				_WT('The uploaded file "test" does not exist.'));

			$oFile = $o->fetch('test');

			$oFile->isOK() or burn('UnitTestException',
				sprintf(_WT('Upload error: %s'), $oFile->getError()));

			try {
				$oFile->getError();
				burn('UnitTestException', _WT('weeUploadedFile::getError should throw an IllegalStateException when file uploaded correctly.'));
			} catch (IllegalStateException $e) {
			}

			$oFile->getExt() == 'txt' or burn('UnitTestException',
				_WT('The extension of the uploaded file is incorrect.'));

			touch('/tmp/test.txt');
			$oFile->fileExists('/tmp/') or burn('UnitTestException',
				_WT('weeUploadedFile::fileExists should have reported that the file exists.'));
			$oFile->moveTo('/tmp/');

			file_get_contents('/tmp/test.txt') == "This file is a test file upload.\n" or burn('UnitTestException',
				_WT('The contents of the uploaded file are incorrect.'));

			unlink('/tmp/test.txt');
		}
	} elseif ($iStep == 2) {
		if ($o->isEmpty()) {
			empty($_GET['upload']) or burn('IllegalStateException',
				_WT('No file was uploaded. Stopping here in order to prevent recursive execution.'));

			// Note: cURL does not seem to support arrays of fields (example: more[])

			$r = curl_init();

			curl_setopt($r, CURLOPT_URL, $sURL);
			curl_setopt($r, CURLOPT_POST, true);
			curl_setopt($r, CURLOPT_POSTFIELDS, array(
				'test' => '@' . dirname(__FILE__) . '/test.txt',
				'more' => '@' . dirname(__FILE__) . '/more.txt',
				'evenmore' => '@' . dirname(__FILE__) . '/evenmore.txt',
			));

			curl_exec($r) or burn('UnitTestException',
				sprintf(_WT('Upload failed: %s'), curl_error($r)));

			curl_close($r);
			exit;
		} else {
			$o->exists('test') or burn('UnitTestException',
				_WT('The uploaded file "test" does not exist.'));

			$o->exists('more') or burn('UnitTestException',
				_WT('The uploaded files "more" does not exist.'));

			$o->exists('evenmore') or burn('UnitTestException',
				_WT('The uploaded files "more" does not exist.'));

			$i = 0;
			foreach ($o as $oFile) {
				$oFile->getExt() == 'txt' or burn('UnitTestException',
					_WT('The extension of the uploaded file is incorrect.'));

				$i++;
			}

			$i == 3 or burn('UnitTestException',
				_WT('The iteration should have passed over 3 different files.'));

			$i = 0;
			foreach ($o->filter('more') as $oFile)
				$i++;

			$i == 1 or burn('UnitTestException',
				_WT('The iteration should have passed over only 1 different file.'));
		}
	}
} catch (Exception $e) {
	echo $e->getMessage();
	exit;
}

echo 'success';
