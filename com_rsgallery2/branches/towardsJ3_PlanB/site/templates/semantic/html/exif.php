<?php defined('_JEXEC') or die(); ?>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="imageExif">
	<tr>
		<th><?php echo JText::_('COM_RSGALLERY2_SECTION'); ?></th>
		<th><?php echo JText::_('COM_RSGALLERY2_NAME'); ?></th>
		<th><?php echo JText::_('COM_RSGALLERY2_VALUE'); ?></th>
	</tr>
<?php
		foreach ($this->exif as $key => $section):
			foreach ($section as $name => $val):
?>
	<tr>
		<td class="exifKey"><?php echo JText::_($key);?></td>
		<td class="exifName"><?php echo JText::_($name);?></td>
		<td class="exifVal"><?php echo JText::_($val);?></td>
	</tr>
<?php
			endforeach;
		endforeach;
?>
</table>