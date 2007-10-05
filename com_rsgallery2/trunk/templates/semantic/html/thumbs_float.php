<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$floatDirection = $rsgConfig->get( 'display_thumbs_floatDirection' );
?>

<ul id='rsg2-thumbsList'>
	<?php foreach( $this->gallery->items as $item ):
		$thumb = $item->thumb(); ?>
		
	<li <?php echo "style='float: $floatDirection'"; ?> >
		<a href="<?php echo sefRelToAbs( "index.php?option=com_rsgallery2&amp;Itemid=$Itemid&amp;page=inline&amp;id=".$item->id ); ?>">
			<!--<div class="img-shadow">-->
			<img border="1" alt="<?php echo htmlspecialchars(stripslashes($item->descr), ENT_QUOTES); ?>" src="<?php echo $thumb->url(); ?>" />
			<!--</div>-->
			<div class="clr"></div>
			<?php if($rsgConfig->get("display_thumbs_showImgName")): ?>
				<br /><span class='rsg2_thumb_name'><?php echo htmlspecialchars(stripslashes($item->title), ENT_QUOTES); ?></span>
			<?php endif; ?>
		</a>
		<?php if( $this->allowEdit ): ?>
		<div id='rsg2-adminButtons'>
			<a href="<?php echo sefRelToAbs("index.php?option=com_rsgallery2&amp;Itemid=$Itemid&amp;page=edit_image&amp;id=".$item->id); ?>"><img src="<?php echo $mosConfig_live_site; ?>/administrator/images/edit_f2.png" alt="" border="0" height="15" /></a>
			<a href="#" onClick="if(window.confirm('<?php echo _RSGALLERY_DELIMAGE_TEXT;?>')) location='<?php echo sefRelToAbs("index.php?option=com_rsgallery2&amp;Itemid=$Itemid&amp;page=delete_image&amp;id=".$item->id); ?>'"><img src="<?php echo $mosConfig_live_site; ?>/administrator/images/delete_f2.png" alt="" border="0" height="15" /></a>
		</div>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
<div class='clr'>&nbsp;</div>