<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Welcome to Web:Extend</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>

	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo APP_PATH?>res/blueprint-css/blueprint/screen.css"/>
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo APP_PATH?>res/blueprint-css/blueprint/print.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo APP_PATH?>res/wee/form.block.css"/>
</head>
<body>
<div id="container" class="container">
	<div class="span-24 last">
		<h1>Welcome to Web:Extend</h1>
	</div>

	<?php if (!empty($error_tmp)):?> 
		<div class="span-24 last notice">
			<h2>Can't write to the temporary directory</h2>
			<p>
				Please make the temporary directory <code><?php echo ROOT_PATH?>app/tmp</code> writable by the web server process.
				On a development environment under Linux this usually means running the following command:
				<pre>chmod o+rw app/tmp</pre>
			</p>
		</div>
	<?php endif?> 

	<?php if (empty($error_db)):?> 
		<div class="span-24 last success">
			<h2>Database set up correctly!</h2>
			<p>
				You can now <a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>/toppage/initdb">create the tables</a>.
				If you have already done that, then please disregard this message.
			</p>
		</div>
	<?php else:?> 
		<div class="span-24 last notice">
			<h2>Can't connect to the database</h2>
			<p>
				The example applications require a database <code>wee_examples</code> available to the user <code>wee</code>
				with the password <code>wee</code>. This database doesn't seem to be setup correctly. Please create the user
				and its database.
			</p>
		</div>
	<?php endif?> 

	<div class="span-24 last">
		<h2>List of examples available:</h2>
		<ol>
			<li><a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>/pastebin">Pastebin</a></li>
		</ol>
	</div>
</div>
</body>
</html>
