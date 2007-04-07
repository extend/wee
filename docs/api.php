<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Web:Extend API</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>

	<style type="text/css">
#container{border:1px solid #ddf;margin:auto;padding:0 1em;width:600px}
div.class h2{border-bottom:1px solid #88f;border-top:1px dotted #88f;padding-top:1em}
h3{border-bottom:1px solid #ddf}
h5{margin:0}
pre{background:#ffffd8;border:1px dashed #88f;font-size:.8em;margin:1em 0;padding:1em}
table,tr,td{border:1px solid #88f;border-collapse:collapse}
th,td{padding:.5em 1em}
th{text-align:right;vertical-align:top}
th[colspan="2"]{text-align:center}
td{text-align:left}
td table *{border:0}
td ul,td li{margin:0;padding:0}
td li table{margin:.5em 0}
	</style>
</head>

<body>
<div id="container">
<h1>Web:Extend API</h1>
<?php

define('ALLOW_INCLUSION', 1);
define('ROOT_PATH', '../');

require(ROOT_PATH . 'wee/wee.php');

$aVariableTypes	= array(
	'a'	=> 'array',
	'b'	=> 'bool',
	'i'	=> 'int',
	'm'	=> 'mixed',
	'o'	=> 'object',
	's'	=> 'string',
);

$aClassTypes = array(
	'abstract'	=> 'abstract class',
	'class'		=> 'class',
	'final'		=> 'final class',
);

$oAPI		= simplexml_load_file('api.xml');
$aClasses	= array();
foreach ($oAPI->classes->children() as $oNode)
	$aClasses[(string)$oNode->name] = $oNode;

ksort($aClasses);

echo '<h2>Summary:</h2><ul id="summary">';
foreach ($aClasses as $oNode)
	echo '<li><a href="#' . $oNode->name . '">' . $aClassTypes[(string)$oNode->type] . ' ' . $oNode->name . '</a></li>';
echo '</ul><div class="classes">';

foreach ($aClasses as $oNode)
{
	echo '<div class="class"><h2 id="' . $oNode->name . '">' . $aClassTypes[(string)$oNode->type] . ' ' . $oNode->name . '</h2>';
	if (!empty($oNode->doccomment))
		echo '<pre>' . $oNode->doccomment . '</pre>';
	echo '<table>';
	echo '<tr><th>filename</th><td>' . $oNode->filename . '</td></tr>';
	echo '<tr><th>lines</th><td>' . $oNode->startline . ' to ' . $oNode->endline . '</td></tr>';
	echo '</table></div>';

	if (!empty($oNode->implements))
	{
		echo '<h3>Implements:</h3><ul>';
		foreach ($oNode->implements->implement as $sInterface)
		{
			echo '<li>' . $sInterface . '</li>';
		}
		echo '</ul>';
	}

	if (!empty($oNode->methods))
	{
		echo '<h3>Methods:</h3><ul>';
		foreach ($oNode->methods->method as $oMethod)
		{
			echo '<li><h4>' . $oMethod->visibility . ' ' . $oMethod->type . ' ' . $oMethod->name . '(';
			if (!empty($oMethod->params))
			{
				$s = null;
				foreach ($oMethod->params->param as $oParam)
				{
					$sName = (string)$oParam->name;
					if (strlen($sName) > 1 && ctype_upper($sName[1]))
						$s .= array_value($aVariableTypes, substr($sName, 0, 1)) . ' ' . $sName;
					else
						$s .= $sName;

					if (isset($oParam->default))
						$s .= ' = ' . $oParam->default;

					$s .= ', ';
				}
				echo substr($s, 0, -2);
			}
			echo ')</h4>';
			if (!empty($oMethod->doccomment))
				echo '<pre>' . $oMethod->doccomment . '</pre>';
			echo '<table>';
			if (empty($oMethod->startline))
				echo '<tr><th colspan="2">Defined by PHP</th></tr>';
			else
				echo '<tr><th>lines</th><td>' . $oMethod->startline . ' to ' . $oMethod->endline . '</td></tr>';
			echo '</table>';
			echo '</li>';
		}
		echo '</ul>';
	}

	if (!empty($oNode->properties))
	{
		echo '<h3>Properties:</h3><ul>';
		foreach ($oNode->properties->property as $oProperty)
			echo '<li><h4>' . $oProperty->visibility . ' ' . array_value($aVariableTypes, substr($oProperty->name, 0, 1)) . ' ' . $oProperty->name . '</h4></li>';
		echo '</ul>';
	}
}

?>
</div>
</div>
</body>
</html>
