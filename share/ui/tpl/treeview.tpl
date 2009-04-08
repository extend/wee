<div class="treeview-container">
	<ul class="treeview">
		<?php $parent_rgts = array(); foreach ($tree as $item):?> 
				<?php while(!empty($parent_rgts) && end($parent_rgts) < $item[$columns['leftid']]): array_pop($parent_rgts)?>
					</ul></li>
				<?php endwhile?> 

				<li><?php echo $item[$columns['label']]?> 
				<div class="items-actions"><?php foreach ($items_actions as $action): if (is_object($item)) $item = $item->toArray()?> 
						<?php if ($action['method'] == 'post'):?> 
							<form action="<?php echo $this->mkLink($action['link'])?>" method="post">
								<?php $keys = array_intersect_key($item, array_flip($primary)); foreach ($keys as $name => $value):?> 
									<input type="hidden" name="<?php echo $name?>" value="<?php echo $value?>"/>
								<?php endforeach?> 
								<input type="submit" value="<?php echo $action['label']?>"/>
							</form>
						<?php else:?> 
							<a href="<?php echo $this->mkLink($action['link'], array_intersect_key($item, array_flip($primary)))?>"><?php echo $action['label']?></a>
						<?php endif?> 
				<?php endforeach?></div>

				<?php if ($item[$columns['leftid']] + 1 == $item[$columns['rightid']]):?> 
					</li>
				<?php else: $parent_rgts[] = $item[$columns['rightid']]?> 
					<ul>
				<?php endif?> 
		<?php endforeach?> 

		<?php while (array_pop($parent_rgts) !== null):?> 
			</ul></li>
		<?endwhile?> 
	</ul>

	<?php if (!empty($global_actions)):?> 
		<ul class="global-actions"><?php foreach ($global_actions as $action):?> 
			<li class="action"><a href="<?php echo $this->mkLink($action['link'])?>"><?php echo $action['label']?></a></li>
		<?php endforeach?></ul>
	<?php endif?> 
</div>
