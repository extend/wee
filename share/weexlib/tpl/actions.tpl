<ul class="actions"><?php foreach ($actions as $action): if (is_object($action['url'])) $action['url'] = $action['url']->toString()?> 
	<li>
		<?php if (!empty($action['method']) && $action['method'] == 'post'):?> 
			<form action="<?php echo $action['url']?>" method="post">
				<?php if (!empty($action['data'])): foreach ($action['data'] as $name => $value):?> 
					<input type="hidden" name="<?php echo $name?>" value="<?php echo $value?>"/>
				<?php endforeach; endif?> 
				<input type="submit" value="<?php echo $action['label']?>"/>
			</form>
		<?php else:?> 
			<a href="<?php echo $action['url']?>"><?php echo $action['label']?></a>
		<?php endif?> 
	</li>
<?php endforeach?></ul>
