<?php
defined('_JEXEC') or die('Restricted access');
global $mainframe, $rsgConfig;
//Add stylesheets and scripts to header
$css1 = "<link rel=\"stylesheet\" href=\"components/com_rsgallery2/templates/slideshow_parth/css/jd.gallery.css\" type=\"text/css\" media=\"screen\" charset=\"utf-8\" />";
$mainframe->AddCustomHeadTag($css1);
$css2 = "<link rel=\"stylesheet\" href=\"components/com_rsgallery2/templates/slideshow_parth/css/template.css\" type=\"text/css\" media=\"screen\" charset=\"utf-8\" />";
$mainframe->AddCustomHeadTag($css2);
$js1 = "<script src=\"components/com_rsgallery2/templates/slideshow_parth/js/mootools.v1.11.js\" type=\"text/javascript\"></script>";
$mainframe->AddCustomHeadTag($js1);
$js2 = "<script src=\"components/com_rsgallery2/templates/slideshow_parth/js/jd.gallery.js\" type=\"text/javascript\"></script>";
$mainframe->AddCustomHeadTag($js2);
?>
<!-- Override default CSS styles -->
<style>
#myGallery, #myGallerySet, #flickrGallery
{
	width: <?php echo $rsgConfig->get('image_width');?>px;
}
</style>
<script type="text/javascript">
	function startGallery() {
		var myGallery = new gallery($('myGallery'), {
			/* Automated slideshow */
			timed: true,
			/* Show the thumbs carousel */
			showCarousel: true,
			/* Text on carousel tab */
			textShowCarousel: 'Thumbs',
			/* Thumbnail height */
			thumbHeight: 50,
			/* Thumbnail width*/
			thumbWidth: 50,
			/* Fade duration in milliseconds (500 equals 0.5 seconds)*/
			fadeDuration: 500,
			/* Delay in milliseconds (6000 equals 6 seconds)*/
			delay: 6000
		});
	}
	window.addEvent('domready',startGallery);
</script>
<div class="content">
	<div id="myGallery">
		<?php echo $this->slides;?>
	</div><!-- end myGallery -->
</div><!-- End content -->