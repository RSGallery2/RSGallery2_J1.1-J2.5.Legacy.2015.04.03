<?php
/**
* Galleries option for RSGallery2
* @version $Id$
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $rsgOptions_path . 'galleries.html.php' );
require_once( $rsgOptions_path . 'galleries.class.php' );

$cid = josGetArrayInts( 'cid' );

switch( $task ){
    case 'new':
        edit( $option, 0 );
        break;

    case 'edit':
        edit( $option, $cid[0] );
        break;

    case 'editA':
        edit( $option, $id );
        break;

    case 'save':
        save( $option );
        break;

    case 'remove':
        removeWarn( $cid, $option );
        break;

    case 'removeReal':
        removeReal( $cid, $option );
        break;

    case 'publish':
        publish( $cid, 1, $option );
        break;

    case 'unpublish':
        publish( $cid, 0, $option );
        break;

    case 'cancel':
        cancel( $option );
        break;

    case 'orderup':
        order( $cid[0], -1, $option );
        break;

    case 'orderdown':
        order( $cid[0], 1, $option );
        break;

	case 'saveorder':
		saveOrder( $cid );
		break;
		
    case 'show':
    default:
        show();
    break;
}

/**
 * show galleries
 * @param database A database connector object
 */
function show(){
    global $database, $mainframe, $mosConfig_list_limit, $option;

    $limit      = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
    $limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
    $levellimit = $mainframe->getUserStateFromRequest( "view{$option}limit", 'levellimit', 10 );
    $search     = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
    $search     = $database->getEscaped( trim( strtolower( $search ) ) );

    // select the records
    // note, since this is a tree we have to do the limits code-side
    if ($search) {
        $query = "SELECT id"
        . "\n FROM #__rsgallery2_galleries"
        . "\n WHERE LOWER( name ) LIKE '%" . strtolower( $search ) . "%'"
        ;
        $database->setQuery( $query );
        $search_rows = $database->loadResultArray();
    }

    $query = "SELECT a.*, u.name AS editor"
    . "\n FROM #__rsgallery2_galleries AS a"
    . "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
    . "\n ORDER BY a.ordering"
    ;
    $database->setQuery( $query );

    $rows = $database->loadObjectList();
    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    // establish the hierarchy of the menu
    $children = array();
    // first pass - collect children
    foreach ($rows as $v ) {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push( $list, $v );
        $children[$pt] = $list;
    }
    // second pass - get an indent list of the items
    $list = mosTreeRecurse( 0, '', array(), $children, max( 0, $levellimit-1 ) );
    // eventually only pick out the searched items.
    if ($search) {
        $list1 = array();

        foreach ($search_rows as $sid ) {
            foreach ($list as $item) {
                if ($item->id == $sid) {
                    $list1[] = $item;
                }
            }
        }
        // replace full list with found items
        $list = $list1;
    }

    $total = count( $list );

    require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
    $pageNav = new mosPageNav( $total, $limitstart, $limit  );

    $lists['levellist'] = mosHTML::integerSelectList( 1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit );

    // slice out elements based on limits
    $list = array_slice( $list, $pageNav->limitstart, $pageNav->limit );

    html_rsg2_galleries::show( $list, $lists, $search, $pageNav );
}


/**
 * Compiles information to add or edit
 * @param integer The unique id of the record to edit (0 if new)
 */
function edit( $option, $id ) {
    global $database, $my, $rsgOptions_path;

    $lists = array();

    $row = new rsgGalleriesItem( $database );
    // load the row from the db table
    $row->load( $id );

    // fail if checked out not by 'me'
    if ($row->isCheckedOut( $my->id )) {
        mosRedirect( 'index2.php?option='. $option, 'The module $row->title is currently being edited by another administrator.' );
    }

    if ($id) {
        $row->checkout( $my->id );
    } else {
        // initialise new record
        $row->published = 1;
        $row->order     = 0;
    }

    // build the html select list for ordering
    $query = "SELECT ordering AS value, name AS text"
    . "\n FROM #__rsgallery2_galleries"
    . "\n ORDER BY ordering"
    ;

	// build list of users
	$lists['uid'] 			= mosAdminMenus::UserSelect( 'uid', $row->uid, 1, NULL, 'name', 0 );
    // build the html select list for ordering
    $lists['ordering']          = mosAdminMenus::SpecificOrdering( $row, $id, $query, 1 );
    // build the html select list for paraent item
    $lists['parent']        = galleryParentSelectList( $row );
    // build the html select list
    $lists['published']         = mosHTML::yesnoRadioList( 'published', 'class="inputbox"', $row->published );

    $file   = $rsgOptions_path .'galleries.item.xml';
    $params = new mosParameters( $row->params, $file, 'component' );

    html_rsg2_galleries::edit( $row, $lists, $params, $option );
}


/**
 * Saves the record on an edit form submit
 * @param database A database connector object
 */
function save( $option ) {
    global $database, $my, $rsgOption, $rsgAccess, $rsgConfig;

    $row = new rsgGalleriesItem( $database );
    if (!$row->bind( $_POST )) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    // save params
    $params = mosGetParam( $_POST, 'params', array() );
    if (is_array( $params )) {
        $txt = array();
        foreach ( $params as $k=>$v) {
            $txt[] = "$k=$v";
        }
        $row->params = implode( "\n", $txt );
    }
	// code cleaner for xhtml transitional compliance
	$row->description = str_replace( '<br>', '<br />', $row->description );
	
    $row->date = date( 'Y-m-d H:i:s' );
    if (!$row->check()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
    }
    $row->checkin();
    $row->updateOrder( );
    
    //If acl is enabled, set permissions array and save them to the DB
    if ( $rsgConfig->get('acl_enabled') ) {
    	$perms = $rsgAccess->makeArrayComplete( mosGetParam( $_POST, 'perm', array() ) );
    	$rsgAccess->savePermissions($perms, $row->id);
    }
	
    mosRedirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}


/**
 * Deletes one or more records
 * @param array An array of unique category id numbers
 * @param string The current url option
 */
function removeWarn( $cid, $option ) {
    if (!is_array( $cid ) || count( $cid ) < 1) {
        echo "<script> alert('Select an item to delete'); window.history.go(-1);</script>\n";
        exit;
    }

    $galleries = rsgGalleryManager::getArray( $cid );

    html_rsg2_galleries::removeWarn( $galleries );
}

/**
* Deletes one or more records
* @param array An array of unique category id numbers
* @param string The current url option
*/
function removeReal( $cid, $option ) {
    global $rsgOption, $rsgConfig;

    $result = rsgGalleryManager::deleteArray( $cid );

    if( !$rsgConfig->get( 'debug' ))
        mosRedirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}

/**
* Publishes or Unpublishes one or more records
* @param array An array of unique category id numbers
* @param integer 0 if unpublishing, 1 if publishing
* @param string The current url option
*/
function publish( $cid=null, $publish=1,  $option ) {
    global $database, $my, $rsgOption;

    $catid = mosGetParam( $_POST, 'catid', array(0) );

    if (!is_array( $cid ) || count( $cid ) < 1) {
        $action = $publish ? 'publish' : 'unpublish';
        echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
        exit;
    }

    $cids = implode( ',', $cid );

    $query = "UPDATE #__rsgallery2_galleries"
    . "\n SET published = " . intval( $publish )
    . "\n WHERE id IN ( $cids )"
    . "\n AND ( checked_out = 0 OR ( checked_out = $my->id ) )"
    ;
    $database->setQuery( $query );
    if (!$database->query()) {
        echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
        exit();
    }

    if (count( $cid ) == 1) {
        $row = new rsgGalleriesItem( $database );
        $row->checkin( $cid[0] );
    }
    mosRedirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}
/**
* Moves the order of a record
* @param integer The increment to reorder by
*/
function order( $uid, $inc, $option ) {
    global $database, $rsgOption;
    $row = new rsgGalleriesItem( $database );
    $row->load( $uid );
    $row->move( $inc, "published >= 0" );

    mosRedirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}

/**
* Cancels an edit operation
* @param string The current url option
*/
function cancel( $option ) {
    global $database, $rsgOption;
    $row = new rsgGalleriesItem( $database );
    $row->bind( $_POST );
    $row->checkin();
    mosRedirect( "index2.php?option=$option&rsgOption=$rsgOption" );
}

function saveOrder( &$cid ) {
	global $database;

	$total		= count( $cid );
	$order 		= josGetArrayInts( 'order' );

	$row 		= new rsgGalleriesItem( $database );
	
	$conditions = array();

	// update ordering values
	for ( $i=0; $i < $total; $i++ ) {
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
			$row->ordering = $order[$i];
			if (!$row->store()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			} // if
			// remember to updateOrder this group
			//$condition = "gallery_id=" . (int) $row->gallery_id;
			$condition = "parent=" . (int) $row->parent;
			$found = false;
			foreach ( $conditions as $cond )
				if ($cond[1]==$condition) {
					$found = true;
					break;
				} // if
			if (!$found) $conditions[] = array($row->id, $condition);
		} // if
	} // for

	// execute updateOrder for each group
	foreach ( $conditions as $cond ) {
		$row->load( $cond[0] );
		$row->updateOrder( $cond[1] );
	} // foreach

	// clean any existing cache files
	mosCache::cleanCache( 'com_rsgallery2' );

	$msg 	= 'New ordering saved';
	mosRedirect( 'index2.php?option=com_rsgallery2&rsgOption=galleries', $msg );
} // saveOrder

?>