<ol class="breadcrumbs">
	<?php $iCount = count($path); $i = 0; foreach ($path as $link => $name): $i++?> 
		<li>
			<?php if ($i == $iCount): echo $name; else:?> 
				<a href="<?php echo $this->mkLink(APP_PATH . $link)?>"><?php echo $name?></a>
			<?php endif?> 
		</li>
	<?php endforeach?> 
</ol>
