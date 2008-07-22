<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Form examples</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP_PATH?>res/wee/wee.css"/>
	<script type="text/javascript" src="<?=APP_PATH?>res/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?=APP_PATH?>res/wee/wee.js"></script>

	<script type="text/javascript">
$(document).ready(function() {
	// fix firefox inline-block
	wee.fixes.inlineblock();
});
	</script>
</head>

<body>
	<ol>
		<li><a href="?type=writable">Writable</a></li>
		<li><a href="?type=selectable">Selectable</a></li>
		<li><a href="?type=misc">Misc</a></li>
	</ol>

	<div id="container">
		<h1>Form widgets:</h1>

		<?php if ($is_submitted):?> 
			<?php if (!empty($errors)):?> 
				<h2>Error:</h2>
				<?php echo nl2uli($errors)?> 
			<?php endif?> 

			<div class="post"><?php var_dump($_POST)?></div>
		<?php endif?> 

		<?php echo $form->toString()?> 
	</div>
</body>
</html>
