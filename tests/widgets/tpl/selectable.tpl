<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Widgets</title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" media="all" href="<?=APP_PATH?>css/wee.css"/>
	<script type="text/javascript" src="<?=APP_PATH?>js/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?=APP_PATH?>js/wee.js"></script>
</head>

<body>
<div id="container">
	<h1>Form widgets:</h1>

	<?php if ($is_submitted):?>
		<?php if (!empty($errors)):?>
			<h2>Error:</h2>
			<?php echo weeUtils::nl2uli($errors)?>
		<?php endif?>

		<div class="post"><?php print_r($_POST)?></div>
	<?php endif?>

	<?php echo $form?>
</div>
</body>
</html>
