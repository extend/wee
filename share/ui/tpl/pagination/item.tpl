<ul class="item-pagination">
	<li class="page">Page <?php echo $current_page + 1?> of <?php echo $total_pages + 1?></li>
	<li class="first<?php if ($current_page <= 0) echo ' disabled'?>">
		<?php if ($current_page > 0):?><a href="<?php echo $this->mkLink($nav_link, array('page' => 0))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('First')?> 
		<?php if ($current_page > 0):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="prev<?php if ($current_page <= 0) echo ' disabled'?>">
		<?php if ($current_page > 0):?><a href="<?php echo $this->mkLink($nav_link, array('page' => $current_page - 1))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Prev')?> 
		<?php if ($current_page > 0):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="next<?php if ($current_page >= $total_pages) echo ' disabled'?>">
		<?php if ($current_page < $total_pages):?><a href="<?php echo $this->mkLink($nav_link, array('page' => $current_page + 1))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Next')?> 
		<?php if ($current_page < $total_pages):?></a><?php else:?></span><?php endif?> 
	</li>
	<li class="last<?php if ($current_page >= $total_pages) echo ' disabled'?>">
		<?php if ($current_page < $total_pages):?><a href="<?php echo $this->mkLink($nav_link, array('page' => $total_pages))?>"><?php else:?><span><?php endif?> 
		<?php echo _WT('Last')?> 
		<?php if ($current_page < $total_pages):?></a><?php else:?></span><?php endif?> 
	</li>
</ul>
