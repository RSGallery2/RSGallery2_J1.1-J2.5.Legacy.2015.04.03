<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="rsg_sem_inl">
	<div class="rsg_sem_inl_dispImg">
    	<?php $this->showDisplayImage(); ?>
	</div>
	<div class="rsg_sem_inl_Nav">
    	<?php $this->showDisplayPageNav(); ?>
	</div>
	<div class="rsg_sem_inl_ImgDetails">
    	<?php $this->showDisplayImageDetails(); ?>
		<?php tagUtils::test ("1"); ?>
    </div>
    
    
    
	<div class="rsg_sem_inl_footer">
    	<?php // $this->showRsgFooter(); ?>
	</div>
</div>