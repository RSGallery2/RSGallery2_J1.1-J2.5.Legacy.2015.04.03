<?php
/**
* Prep for slideshow
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_VALID_MOS' ) or die( 'Restricted Access' );

// bring in display code
$templatePath = RSG2_PATH_SITE . DS . 'templates' . DS . 'slideshowone';
require_once( $templatePath . DS . 'display.class.php');

$rsgDisplay = new rsgDisplay_slideshowone();

$rsgDisplay->cleanStart = rsgInstance::getBool( 'cleanStart' );

$rsgDisplay->showSlideShow();
