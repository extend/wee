<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Pastebin Demo</title>
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
		<h1>Pastebin Demo</h1>

		<?if ($is_submitted):?> 
			<?if (!empty($errors)):?> 
				<h2>Pastebin Error!</h2>

				<div class="errors"><p><?=nl2br($errors)?></p></div>
			<?else:?> 
				<h2>Pastebin Posted!</h2>

				<div class="posted"><p>
					The <a href="<?=APP_PATH?>index<?=PHP_EXT?>/view?id=<?=$posted_id?>">Pastebin #<?=$posted_id?></a> has been created.
				</p></div>
			<?endif?> 
		<?else:?> 
			<h2>What is it?</h2>

			<p>Pastebin is a collaborative tool used to paste large chunks of text that can't be pasted
			on other medium (like IRC) for other people to see.</p>
			<p>Pastebin is mostly used as a debugging tool by pasting code or configuration files for maintainers
			or other developers to see. You are free to use this tool for your own use, but you must be aware that
			anyone can see what you post.</p>
			<p>Please be aware that code posted here is subject to various licenses. Do not use it if you don't
			know where it comes from or if the license don't permit its reuse. If you post code, please add the
			license in a comment to the code you post at the begin of the code pasted.</p>
		<?endif?> 

		<h2>Find a Pastebin</h2>

		<form action="<?=APP_PATH?>index<?=PHP_EXT?>/view" class="finder" method="get">
			<label for="id">Number: <input class="number" id="id" name="id" type="text"/></label>
			<input type="submit"/>
		</form>

		<p><a href="<?=APP_PATH?>index<?=PHP_EXT?>/last">View Last 5 Pastebins</a></p>

		<h2>Post a new Pastebin!</h2>
		<?=$form->toString()?> 
	</div>
</body>
</html>
