<div class="list-container">
	<?php reset($frames)->render()?> 

	<table class="list">
		<thead>
			<tr>
				<?php foreach ($columns as $label => $name): if (is_int($label)) $label = $name;?> 
					<th>
						<a href="<?php echo $this->mkLink($_SERVER['REQUEST_URI'], array(
							'orderby' => $name,
							'orderdirection' => $orderby != $name || empty($orderdirection) || $orderdirection == 'desc' ? 'asc' : 'desc',
						))?>"<?php if ($orderby == $name):?> class="<?php echo $orderdirection?>"<?php endif?>><?php echo $label?></a>
					</th>
				<?php endforeach?> 

				<?php if (!empty($items_actions)):?><th class="items-actions"><?php echo _WT('Actions')?></th><?php endif?> 
			</tr>
		</thead>

		<tbody>
			<?php if (count($list) == 0):?> 
				<tr class="empty-list"><td colspan="<?php echo count($columns) + (int)!empty($items_actions)?>"><?php echo _WT('The list is empty.')?></td></tr>
			<?php else: foreach ($list as $item):?> 
				<tr>
					<?php foreach ($columns as $name):?> 
						<td><?php echo $item[$name]?></td>
					<?php endforeach?> 

					<?php if (!empty($items_actions)): if (is_object($item)) $item = $item->toArray()?><td class="items-actions">
						<ul><?php foreach ($items_actions as $action):?> 
							<li class="action">
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
							</li>
						<?php endforeach?></ul>
					</td><?php endif?> 
				</tr>
			<?php endforeach; endif?> 
		</tbody>

		<?php if (!empty($global_actions)):?><tfoot>
			<tr class="global-actions">
				<td colspan="<?php echo count($columns) + (int)!empty($items_actions)?>">
					<ul><?php foreach ($global_actions as $action):?> 
						<li class="action"><a href="<?php echo $this->mkLink($action['link'])?>"><?php echo $action['label']?></a></li>
					<?php endforeach?></ul>
				</td>
			</tr>
		</tfoot><?php endif?> 
	</table>

	<?php reset($frames)->render()?> 
</div>
