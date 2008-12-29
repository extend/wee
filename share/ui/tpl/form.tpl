<?php if (empty($is_submitted)): echo $form->toString(); else:?> 
	<?php if (empty($errors)):?> 
		<div class="msg">
			<?php echo _WT('The form has been successfully submitted.')?> 
		</div>
	<?php else:?> 
		<div class="errors">
			<ul><li><?php echo str_replace("\r\n", '</li><li>', $errors)?></li></ul>
		</div>

		<?php echo $form->toString()?> 
	<?php endif?> 
<?php endif?> 
