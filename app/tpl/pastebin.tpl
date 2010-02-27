<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Pastebin example</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>

	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo APP_PATH?>res/blueprint-css/blueprint/screen.css"/>
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo APP_PATH?>res/blueprint-css/blueprint/print.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo APP_PATH?>res/wee/form.block.css"/>
</head>
<body>
<div id="container" class="container">
	<div class="span-24 last">
		<h1>Pastebin example</h1>
	</div>

	<div class="span-6">
		<?php if (count($last_pastebins) == 0):?> 
			<div class="notice">
				<h2>Welcome!</h2>
				<p>Welcome to the pastebin example.</p>
				<p>It seems no pastebin has been posted yet. Try it!</p>
			</div>
		<?php else:?> 
			<h2>Recent posts</h2>
			<ul><?php foreach ($last_pastebins as $pb):?> 
				<li><a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>/pastebin/<?php echo $pb['pastebin_id']?>">
					<?php echo $pb['pastebin_timestamp']?> 
				</a></li>
			<?php endforeach?></ul>
		<?php endif?> 
	</div>

	<div class="prepend-1 span-17 last">
		<?php if (empty($form)):?> 
			<div class="notice">
				<h2>Pastebin number #<?php echo $pastebin['pastebin_id']?></h2>
				<p>Posted on <?php echo $pastebin['pastebin_timestamp']?></p>
				<ul>
					<li><a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>/pastebin">New post</a></li>
					<li><a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>/pastebin/download/<?php echo $pastebin['pastebin_id']?>">Download as text</a></li>
				</ul>
			</div>

			<pre><?php echo str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $pastebin['pastebin_text'])?></pre>
		<?php else:?> 
			<?php if (empty($success)):?> 
				<div class="notice">
					<h2>About the pastebin example</h2>
					<p>
						A pastebin is a collaborative tool used to paste large chunks of text subsequently
						available through a tiny link that you can then give to your collaborators.
					</p>
				</div>
			<?php else:?> 
				<div class="success">
					<h2>Pastebin created successfully!</h2>
					<p>
						The pastebin is available at the following URL:
						<a href="<?php echo APP_PATH?>index<?php echo PHP_EXT?>/pastebin/<?php echo $pastebin['pastebin_id']?>">
							<?php echo APP_PATH?>index<?php echo PHP_EXT?>/pastebin/<?php echo $pastebin['pastebin_id']?> 
						</a>
					</p>
				</div>
			<?php endif?> 

			<?php $form->render()?> 
		<?php endif?> 
	</div>
</div>
</body>
</html>
