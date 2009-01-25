<?php foreach ($frames as $name => $frame):?> 
	<div id="<?php echo $name?>"><?php $frame->render()?></div>
<?php endforeach?> 
