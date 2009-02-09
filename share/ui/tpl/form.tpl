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

<?php if (!empty($debug)):?> 
	<div class="debug">
		<a href="<?php echo $this->mkLink($_SERVER['REQUEST_URI'], empty($xmloutput) ? array('output' => 'xml') : array())?>">View Form's XML</a>

		<?php if (!empty($xmloutput)):?> 
			<pre><?php echo $xmloutput?></pre>
		<?php endif?> 
	</div>
<?php endif?> 
