<table class="data_list">
	<thead><tr>
		<?php foreach ($data_list['columns'] as $label => $name): if (is_int($label)) $label = $name;?> 
			<th><a href="<?php echo $data_list['url']->addData(array(
				'order' => $data_list['sort'] != $name || empty($data_list['order']) || $data_list['order'] == 'desc' ? 'asc' : 'desc',
				'sort' => $name,
			))->toString()?>"<?php if ($data_list['sort'] == $name):?> class="<?php echo $data_list['order']?>"<?php endif?>>
				<?php echo $label?> 
			</a></th>
		<?php endforeach?> 

		<?php $b = false; foreach ($data_list['data'] as $item): if (!empty($item['row_actions'])): $b = true; break; endif; endforeach; if ($b):?><th><?php echo _WT('Actions')?></th><?php endif?> 
	</tr></thead>

	<tbody>
		<?php if (count($data_list['data']) == 0):?> 
			<tr class="empty-list"><td colspan="<?php echo count($data_list['columns']) + (int)!empty($items_actions)?>"><?php echo _WT('The list is empty.')?></td></tr>
		<?php else: foreach ($data_list['data'] as $item):?> 
			<tr>
				<?php foreach ($data_list['columns'] as $name):?> 
					<td><?php echo $item[$name]?></td>
				<?php endforeach?> 

				<?php if (!empty($item['row_actions'])):?> 
					<td><?php $this->template('weexlib/actions', array('actions' => $item['row_actions']))?></td>
				<?php endif?> 
			</tr>
		<?php endforeach; endif?> 
	</tbody>

	<?php if (!empty($data_list['actions'])):?><tfoot><tr class="global-actions">
		<td colspan="<?php echo count($data_list['columns']) + $b?>">
			<?php $this->template('weexlib/actions', array('actions' => $data_list['actions']))?> 
		</td>
	</tr></tfoot><?php endif?> 
</table>
