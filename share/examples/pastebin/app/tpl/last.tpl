<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Last 5 Pastebins</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>

	<link rel="stylesheet" type="text/css" media="all" href="<?=APP_PATH?>res/wee/wee.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP_PATH?>pub/pastebin.css"/>
	<script type="text/javascript" src="<?=APP_PATH?>res/wee/wee.js"></script>

	<!-- compliance patch for microsoft browsers -->
	<!--[if lt IE 8]>
		<script type="text/javascript">
			document.write('<style>body{visibility:hidden}html>body{visibility:visible}</style>');
		</script>
		<script src="<?=APP_PATH?>res/ie7/ie7-core.js" type="text/javascript"></script>
		<script src="<?=APP_PATH?>res/ie7/ie7-css2-selectors.js" type="text/javascript"></script>
	<![endif]-->
</head>
<body>
	<div id="container">
		<?foreach ($pastebins as $paste):?> 
			<h1>Pastebin Number #<?=$paste['data_id']?></h1>

			<div class="timestamp">Posted on: <span><?=$paste['data_timestamp']?></span></div>
			<pre class="pastebin"><?=str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $paste['data_text'])?></pre>
			<hr/>
		<?endforeach?> 

		<div class="links"><a href="<?=APP_PATH?>index<?=PHP_EXT?>" title="Pastebin Demo">Post a new Pastebin!</a></div>
	</div>
</body>
</html>
