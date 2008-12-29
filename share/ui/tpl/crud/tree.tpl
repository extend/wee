<div class="tree-container">
	<table class="list">
		<thead>
			<tr>
				<th><?php echo _WT('Items')?></th>
				<th class="items-actions"><?php echo _WT('Actions')?></th>
			</tr>
		</thead>

		<tbody>
			<?php $count = count($tree); if ($count == 0):?> 
				<tr class="empty-list"><td colspan="<?php echo 1 + (int)!empty($items_actions)?>"><?php echo _WT('The list is empty.')?></td></tr>
			<?php else:?> 
				<tr>
					<td class="tree" rowspan="<?php echo count($tree)?>">
						<ol><?php $prev_lft = 0; foreach ($tree as $item):?> 
							<li>
								<?php echo $item[$columns['label']]?> 
								<?php if ($item[$columns['left']] + 1 == $item[$columns['right']] && $prev_lft + 2 != $item[$columns['right']]):?> 
									</ol>
								<?php else:?> 
									<ol>
								<?php endif?> 
							</li>
						<?php endforeach?></ol>
					</td>

					<?php $i = 0; foreach($tree as $item): $i++; if (is_object($item)) $item = $item->toArray()?> 
						<td class="items-actions">
							<ul>
								<li class="action"><a href="<?php echo $this->mkLink($frame . '/up', array_intersect_key($item, array_flip($primary)))?>"><?php echo _WT('Up')?></a></li>
								<li class="action"><a href="<?php echo $this->mkLink($frame . '/down', array_intersect_key($item, array_flip($primary)))?>"><?php echo _WT('Down')?></a></li>
								<li class="action"><a href="<?php echo $this->mkLink($frame . '/update', array_intersect_key($item, array_flip($primary)))?>"><?php echo _WT('Update')?></a></li>

								<li class="action">
									<form action="<?php echo $this->mkLink($frame . '/delete')?>" method="post">
										<?php $keys = array_intersect_key($item, array_flip($primary)); foreach ($keys as $name => $value):?> 
											<input type="hidden" name="<?php echo $name?>" value="<?php echo $value?>"/>
										<?php endforeach?> 
										<input type="submit" value="<?php echo _WT('Delete')?>"/>
									</form>
								</li>
							</ul>

						</td><?php if ($i != $count):?></tr><tr><?php endif?> 
					<?php endforeach?> 
				</tr>
			<?php endif?> 
		</tbody>

		<tfoot>
			<tr class="global-actions">
				<td colspan="2">
					<ul>
						<li class="action"><a href="<?php echo $this->mkLink($frame . '/add')?>"><?php echo _WT('Create')?></a></li>
					</ul>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
