<ul class="item-pagination">
	<li class="page"><?php echo sprintf(_WT('%d-%d of %d'), $from + 1, min($from + $countperpage, $total), $total)?></li>
	<li class="first<?php if ($from == 0) echo ' disabled'?>">
		<?php if ($from > 0):?><a href="<?php echo $this->mkLink($url, array('from' => 0))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('First')?> 
		<?php if ($from > 0):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="prev<?php if ($from == 0) echo ' disabled'?>">
		<?php if ($from > 0):?><a href="<?php echo $this->mkLink($url, array('from' => max(0, $from - $countperpage)))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Prev')?> 
		<?php if ($from > 0):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="next<?php if ($from >= $total - $countperpage) echo ' disabled'?>">
		<?php if ($from < $total - $countperpage):?><a href="<?php echo $this->mkLink($url, array('from' => $from + $countperpage))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Next')?> 
		<?php if ($from < $total - $countperpage):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="last<?php if ($from >= $total - $countperpage) echo ' disabled'?>">
		<?php if ($from < $total - $countperpage):?><a href="<?php
			$mod = $total % $countperpage;
			if ($mod == 0)
				$mod = $countperpage;
			echo $this->mkLink($url, array('from' => $total - $mod))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Last')?> 
		<?php if ($from < $total - $countperpage):?></a><?php else:?></span><?php endif?> 
	</li>
</ul>
