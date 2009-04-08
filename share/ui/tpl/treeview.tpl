<div class="treeview-container">
	<ol class="treeview">
		<?php $parent_rgts = array(count($tree) * 2 + 1); $prev_rgt = -1; foreach ($tree as $item): if (is_object($item)) $item = $item->toArray()?> 
				<?php while($item[$columns['leftid']] > end($parent_rgts)): $prev_rgt = array_pop($parent_rgts)?>
					</ol></li>
				<?php endwhile?>

				<li><?php echo $item[$columns['label']]?>
				<div class="items-actions"><?php foreach ($items_actions as $action):?>
					<?php if ($action['label'] == _WT('Up') && $item[$columns['leftid']] != $prev_rgt + 1) continue?>
					<?php if ($action['label'] == _WT('Down') && $item[$columns['rightid']] == end($parent_rgts) - 1) continue?>

					<?php $keys = array_intersect_key($item, array_flip($primary)); if ($action['method'] == 'post'):?> 
						<form action="<?php echo $this->mkLink($action['link'])?>" method="post">
							<?php foreach ($keys as $name => $value):?> 
								<input type="hidden" name="<?php echo $name?>" value="<?php echo $value?>"/>
							<?php endforeach?> 
							<input type="submit" value="<?php echo $action['label']?>"/>
						</form>
					<?php else:?> 
						<a href="<?php echo $this->mkLink($action['link'], $keys)?>"><?php echo $action['label']?></a>
					<?php endif?> 
				<?php endforeach?></div>

				<?php if ($item[$columns['leftid']] + 1 == $item[$columns['rightid']]): $prev_rgt = $item[$columns['rightid']]?> 
					</li>
				<?php else: $parent_rgts[] = $item[$columns['rightid']]?> 
					<ol>
				<?php endif?> 
		<?php endforeach?> 

		<?php while (isset($parent_rgts[1])):?> 
			</ol></li>
		<?array_pop($parent_rgts); endwhile?> 
	</ul>

	<?php if (!empty($global_actions)):?> 
		<ul class="global-actions"><?php foreach ($global_actions as $action):?> 
			<li class="action"><a href="<?php echo $this->mkLink($action['link'])?>"><?php echo $action['label']?></a></li>
		<?php endforeach?></ul>
	<?php endif?> 
</div>
