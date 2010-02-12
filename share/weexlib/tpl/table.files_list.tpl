<table class="files_list">
	<thead><tr>
		<th class="name"><?php echo _WT('Name')?></th>
		<th class="size"><?php echo _WT('Size')?></th>
		<th class="type"><?php echo _WT('Type')?></th>
		<th class="date"><?php echo _WT('Date Modified')?></th>
		<?php $b = false; foreach ($files_list as $file): if (!empty($file['row_actions'])): $b = true; break; endif; endforeach; if ($b):?><th class="actions"><?php echo _WT('Actions')?></th><?php endif?> 
	</tr></thead>
	<tbody>
		<?php $i = 1; foreach ($files_list as $file):?><tr<?php if ($i++ % 2 == 0) echo ' class="even"'?>>
			<td class="name">
				<span class="icon <?php echo $file['icon']?>">&nbsp;</span>
				<?php if (!empty($file['url'])):?><a href="<?php echo $file['url']?>"><?php endif?><?php echo $file['filename']?><?php if (!empty($file['url'])):?></a><?php endif?> 
			</td>
			<td class="size"><?php echo $file['husize']?></td>
			<td class="type"><?php echo $file['mimetype']?></td>
			<td class="date"><?php echo $file['humtime']?></td>
			<?php if (!empty($file['row_actions'])):?> 
				<td class="actions"><?php $this->template('weexlib/actions', array('actions' => $file['row_actions']))?></td>
			<?php endif?> 
		</tr><?php endforeach?> 
	</tbody>
</table>
