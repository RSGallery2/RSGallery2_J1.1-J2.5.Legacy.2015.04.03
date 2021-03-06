<?php
/**
* This file contains the template information for photoBox to be used with RSGallery2
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_VALID_MOS' ) or die( 'Restricted Access' );

// bring in display code
$templatePath = JPATH_RSGALLERY2_SITE . DS . 'templates' . DS . 'photobox';
require_once( $templatePath . DS . 'display.class.php');

$rsgDisplay = new rsgDisplay_photoBox();

global $mosConfig_live_site;
$template_dir = "$mosConfig_live_site/components/com_rsgallery2/templates/photobox";
$lightbox_dir = "$mosConfig_live_site/components/com_rsgallery2/lib/lightbox_plus";

$rsgDisplay->metadata();
// append to Joomla's pathway
$rsgDisplay->showRSPathWay();

//Load overlib routine for Tooltips
mosCommonHTML::loadOverlib();
?>

<?php //image display ?>
<script type="text/javascript" src="<?php echo $template_dir; ?>/js/selectThumb.js"></script>
<script type="text/javascript" src="<?php echo $lightbox_dir; ?>/spica.js"></script>
<script type="text/javascript" src="<?php echo $lightbox_dir; ?>/lightbox_plus.js"></script>

<link href="<?php echo $template_dir; ?>/css/template.css" rel="stylesheet" type="text/css" />

<div class='rsg2'>
	<?php $rsgDisplay->mainPage(); ?>
</div>