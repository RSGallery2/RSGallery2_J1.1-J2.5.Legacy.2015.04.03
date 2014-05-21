<script language="javascript" type="text/javascript">
<!--
	function submitbutton3(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_directory.value == ""){
			alert( "<?php echo JText::_( 'Please select a directory', true ); ?>" );
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	function submitbutton4(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert( "<?php echo JText::_( 'Please enter a URL', true ); ?>" );
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}
//-->
</script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_RSGALLERY2_UPLOAD_PACKAGE_FILE' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_package"><?php echo JText::_( 'COM_RSGALLERY2_PACKAGE_FILE' ); ?>:</label>
		</td>
		<td>
			<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			<input class="button" type="button" value="<?php echo JText::_( 'COM_RSGALLERY2_UPLOAD_FILE' ); ?> &amp; <?php echo JText::_( 'COM_RSGALLERY2_INSTALL' ); ?>" onclick="submitbutton()" />
		</td>
	</tr>
	</table>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_RSGALLERY2_INSTALL_FROM_DIRECTORY' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_directory"><?php echo JText::_( 'COM_RSGALLERY2_INSTALL_DIRECTORY' ); ?>:</label>
		</td>
		<td>
			<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />
			<input type="button" class="button" value="<?php echo JText::_( 'COM_RSGALLERY2_INSTALL' ); ?>" onclick="submitbutton3()" />
		</td>
	</tr>
	</table>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_RSGALLERY2_INSTALL_FROM_URL' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_url"><?php echo JText::_( 'COM_RSGALLERY2_INSTALL_URL' ); ?>:</label>
		</td>
		<td>
			<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
			<input type="button" class="button" value="<?php echo JText::_( 'COM_RSGALLERY2_INSTALL' ); ?>" onclick="submitbutton4()" />
		</td>
	</tr>
	</table>

	<input type="hidden" name="type" value="" />
	<input type="hidden" name="installtype" value="upload" />
	<input type="hidden" name="task" value="doInstall" />
	<input type="hidden" name="option" value="com_rsgallery2" />
	<input type="hidden" name="rsgOption" value="installer" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
