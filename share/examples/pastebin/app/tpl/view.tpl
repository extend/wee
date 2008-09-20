<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Pastebin Number: <?php echo $paste['data_id']?></title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>

	<link rel="stylesheet" type="text/css" media="all" href="<?php echo APP_PATH?>res/wee/wee.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo APP_PATH?>pub/pastebin.css"/>

	<!-- compliance patch for microsoft browsers -->
	<!--[if lt IE 8]>
		<script type="text/javascript">
			document.write('<style>body{visibility:hidden}html>body{visibility:visible}</style>');
		</script>
		<script src="<?php echo APP_PATH?>res/ie7/ie7-core.js" type="text/javascript"></script>
		<script src="<?php echo APP_PATH?>res/ie7/ie7-css2-selectors.js" type="text/javascript"></script>
	<![endif]-->
</head>
<body>
	<div id="container">
		<?if (empty($error)):?> 
			<h1>Pastebin Number #<?php echo $paste['data_id']?></h1>

			<div class="timestamp">Posted on: <span><?php echo $paste['data_timestamp']?></span></div>
			<pre class="pastebin"><?php echo str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $paste['data_text'])?></pre>
			<div class="links"><a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>" title="Pastebin Demo">Post a new Pastebin!</a></div>
		<?else:?> 
			<h1>Pastebin Error!</h1>

			<div class="errors"><p><?php echo $error?></p></div>
			<div class="links"><a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>" title="Pastebin Demo">Find another Pastebin!</a></div>
		<?endif?> 
	</div>
</body>
</html>
