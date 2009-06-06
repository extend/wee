<?php empty($frames) and burn('IllegalStateException', _WT('Missing $frames variable. This template should only be used along with a weeCRUDUI frame or equivalent.'));

	  foreach ($frames as $name => $frame):?> 
	<div id="<?php echo $name?>"><?php $frame->render()?></div>
<?php endforeach?> 
