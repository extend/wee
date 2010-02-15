<ul class="item-pagination">
	<li class="page"><?php echo sprintf(_WT('Items %d-%d of %d'), $pagination['from'] + 1, min($pagination['from'] + $pagination['max'], $pagination['total']), $pagination['total'])?></li>
	<li class="first<?php if ($pagination['from'] == 0) echo ' disabled'?>">
		<?php if ($pagination['from'] > 0):?><a href="<?php echo $pagination['url']->addData(array('from' => 0))->toString()?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('First')?> 
		<?php if ($pagination['from'] > 0):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="prev<?php if ($pagination['from'] == 0) echo ' disabled'?>">
		<?php if ($pagination['from'] > 0):?><a href="<?php echo $pagination['url']->addData(array('from' => max(0, $pagination['from'] - $pagination['max'])))->toString()?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Prev')?> 
		<?php if ($pagination['from'] > 0):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="next<?php if ($pagination['from'] >= $pagination['total'] - $pagination['max']) echo ' disabled'?>">
		<?php if ($pagination['from'] < $pagination['total'] - $pagination['max']):?><a href="<?php echo $pagination['url']->addData(array('from' => $pagination['from'] + $pagination['max']))->toString()?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Next')?> 
		<?php if ($pagination['from'] < $pagination['total'] - $pagination['max']):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="last<?php if ($pagination['from'] >= $pagination['total'] - $pagination['max']) echo ' disabled'?>">
		<?php if ($pagination['from'] < $pagination['total'] - $pagination['max']):?><a href="<?php
			$mod = $pagination['total'] % $pagination['max'];
			if ($mod == 0)
				$mod = $pagination['max'];
			echo $pagination['url']->addData(array('from' => $pagination['total'] - $mod))->toString()?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Last')?> 
		<?php if ($pagination['from'] < $pagination['total'] - $pagination['max']):?></a><?php else:?></span><?php endif?> 
	</li>
</ul>
