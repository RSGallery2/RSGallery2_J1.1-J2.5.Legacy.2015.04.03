<?php
/**
* category class
* @version $Id$
* @package RSGallery2
* @copyright (C) 2005 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* Image database table class
* @package RSGallery2
* @author Ronald Smit <ronald.smit@rsdev.nl>
*/
class rsgImagesItem extends mosDBTable {
	/** @var int Primary key */
	var $id					= null;
	/** @var int */
	var $name				= null;
	/** @var int */
	var $descr				= null;
	/** @var string */
	var $gallery_id			= null;
	/** @var string */
	var $title				= null;
	/** @var string */
	var $hits				= null;
	/** @var datetime */
	var $date				= null;
	/** @var int */
	var $rating				= null;
	/** @var int */
	var $votes				= null;
	/** @var int */
	var $comments			= null;
	/** @var boolean */
	var $published			= null;
	/** @var boolean */
	var $checked_out		= null;
	/** @var time */
	var $checked_out_time	= null;
	/** @var int */
	var $approved			= null;
	/** @var boolean */
	var $ordering			= null;
	/** @var string */
	var $params				= null;

	/**
	* @param database A database connector object
	*/
	function rsgImagesItem( &$db ) {
		$this->mosDBTable( '#__rsgallery2_files', 'id', $db );
	}
	/** overloaded check function */
	function check() {
		// filter malicious code
		$ignoreList = array( 'params','descr' );
		$this->filter( $ignoreList );

		/** check for valid name */
		if (trim( $this->title ) == '') {
			$this->_error = '*Please provide a valid image title*';
			return false;
		}

		return true;
	}
}
?>
