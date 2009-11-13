<html>
<head>
	<title>Example</title>
	<script type="text/javascript" src="<?php echo APP_PATH?>res/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH?>res/jquery/jquery.taconite.js"></script>
	<script type="text/javascript" src="<?php echo APP_PATH?>res/jquery/jquery.form.js"></script>
	<script type="text/javascript">
function ajaxify(){
	$('form').ajaxForm(function(){
		// We need to ajaxify again when our form gets replaced
		ajaxify();
	});
}

$(function(){
	// The following function will be called when the document is loaded
	ajaxify();
});
	</script>
</head>

<body>
	<p id="msg">Please edit the resource and submit the form.</p>
	<?php echo $form->toString()?> 
</body>
</html>
