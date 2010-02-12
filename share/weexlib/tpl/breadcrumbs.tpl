<ol class="breadcrumbs">
	<li><strong><?php echo _WT('Navigation')?></strong></li>
	<?php $iCount = count($breadcrumbs); $i = 0; foreach ($breadcrumbs as $name => $link): $i++?> 
	<li><?php if ($i == $iCount): echo $name; else:?> 
		<a href="<?php $this->url($link)?>"><?php echo $name?></a>
	<?php endif?></li>
<?php endforeach?></ol>
