<?php
/**
* Main task switch for RSGallery2
* @version $Id: main.rsgallery2.php 1085 2012-06-24 13:44:29Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/
defined( '_JEXEC' ) or die();

/**
 * this is the primary and default function
 * it loads a template to run
 * that template's rsgDisplay has a switch for $page to handle various features
 * @throws Exception
 */
function template(){
	global $rsgConfig;

	//Set template selection
	//$template = preg_replace( '#\W#', '', JRequest::getCmd( 'rsgTemplate', $rsgConfig->get('template') ));
	$input =JFactory::getApplication()->input;
	$template = preg_replace( '#\W#', '', $input->get( 'rsgTemplate', $rsgConfig->get('template'), 'CMD'));
	$template = strtolower( $template );

	$templateLocation = JPATH_RSGALLERY2_SITE . DS . 'templates' . DS . $template . DS . 'index.php';

	if( !file_exists( $templateLocation ))
		JError::raiseError( '', "Template $template does not exist.<br/>Please select an existing template in the Template Manager." );
	else
		require( $templateLocation );
}

/**
 * @throws Exception
 */
function xmlFile(){
	// $template = preg_replace( '#\W#', '', JRequest::getCmd( 'xmlTemplate', 'meta' ) );
	$input =JFactory::getApplication()->input;
	$template = preg_replace( '#\W#', '', $input->get( 'xmlTemplate', 'meta', 'CMD'));	
	$template = strtolower( $template );
	
	// require generic template which all other templates should extend
	require_once( JPATH_RSGALLERY2_SITE . DS . 'templates' . DS . 'meta' . DS . 'xml.php' );
	// require the template specified to be used
	require_once( JPATH_RSGALLERY2_SITE . DS . 'templates' . DS . $template . DS . 'xml.php' );
	
	// prepare and output xml
	$xmlTemplate = "rsgXmlGalleryTemplate_$template";
	$gallery = rsgGalleryManager::get();
	// $xmlTemplate = new $xmlTemplate( rsgInstance::getGallery() ); getGallery deprecated
	$xmlTemplate = new $xmlTemplate( $gallery );

	ob_start();
	$xmlTemplate->prepare();
	$content = ob_get_clean();

	$xmlTemplate->printHead();
	echo $content;
	
	die(); 	// quit now so that only the xml is sent and not the joomla template
			// this is a saftey measure that should not be needed
}

/**
 * Forces a download box to download single images
 * Thanks to Rich Malak <rmalak@fuseideas.com> for his invaluable contribution
 * to this very important feature!
 */
//function downloadFile($id) {
function downloadFile() {
	global $rsgConfig;
	//Clean and delete current output buffer 
	ob_end_clean();
	
	$gallery = rsgGalleryManager::getGalleryByItemID();
	$item = $gallery->getItem();
	$original = $item->original();
	$file = $original->filePath();
	
	//Open up the file
	if ( $fd = fopen($file, "r") ) {
	    $fsize 		= filesize($file);
	    $path_parts = pathinfo($file);
	    $ext 		= strtolower($path_parts["extension"]);
	    
	    //Check the extension and provide the right headers for the file
	    switch ($ext) {
	        case "pdf":
	        	header("Content-type: application/pdf"); // add here more headers for diff. extensions
	        	header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachement' to force a download
	        	break;
	        case "jpg":
	        	header("Content-type: image/jpeg");
	        	header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
	        	break;
	        case "gif":
	        	header("Content-type: image/gif");
	        	header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
	        	break;
	        case "png":
	        	header("Content-type: image/png");
	        	header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
	        	break;
	    	default:
	        	header("Content-type: application/octet-stream");
	        	header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
	    }
	    header("Content-length: $fsize");
	    header("Cache-control: private");
	    //Read the contents of the file
	    while(!feof($fd)) {
	        $buffer = fread($fd, 4096);
	        echo $buffer;
	    }
	}
	//Close file after use!
	fclose ($fd);
}
