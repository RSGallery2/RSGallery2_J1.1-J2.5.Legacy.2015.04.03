<?php
/**
 * @version		$Id: router.php 7380 2007-05-06 21:26:03Z eddieajau $
 * @package		Joomla changed by RSGallery2
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

 /*		If gid, and it’s not part of a menulink: add ‘gallery’ (category was used <= v2.1.1) and add gid number
		If id then add ‘item’ and id number
		If start then add ‘itemPage’ and limitstart value - 1
		If page then add ‘as’ concatenated with page value
  */
 
function Rsgallery2BuildRoute(&$query)
{
	static $items;
	
	$currentMenu = null;
	$segments	= array();
	$itemid		= isset($query['Itemid']) ? $query['Itemid'] : null;
	
	// Get the menu items for this component.
	if (!$items) {
		$component	= &JComponentHelper::getComponent('com_rsgallery2');
		$menu		= &JSite::getMenu();
		$items		= $menu->getItems('componentid', $component->id);
	}
	
	if($itemid != null){
		// get the right menu item if multiple galliers exist
		foreach($items as $key => $item)
		{
			if($item->id == $itemid){
				$currentMenu = $item;
				break;
			}				
		}
	}
	
	// rename catId to gId	//catId could be leftover from versions before 1.14.x
	if(isset($query['catid'])){
		$query['gid'] = $query['catid'];
		unset($query['catid']);
	}
	
	// direct gallery link
	if(isset($query['gid'])){
		// add the gallery id only if it is not part of the menu link
		if(empty($item) ||
				preg_match( "/gid=".$query['gid']."/", $currentMenu->link) == 0){// changed from "/gid=([0-9]*)/" to have an exact match
			$segments[] = 'gallery';
			$segments[] = Rsgallery2GetCategoryName($query['gid']);
		}
		unset($query['gid']);
	}
	
	// gallery paging	
	if(isset($query['limitstartg'])){
		$segments[] = 'categoryPage';
		$segments[] = $query['limitstartg'];
		unset($query['limitstartg']);
	}
	
	// direct item link
	if(isset($query['id'])){
		$segments[] = 'item';
		$segments[] = Rsgallery2GetItemName($query['id']);
		unset($query['id']);
	}
	
	// item paging
	if(isset($query['start'])){
		$segments[] = 'itemPage';
		$segments[] = $query['start'];
		unset($query['start']);
	}
	
	// how to show the item
	if(isset($query['page'])){
		$segments[] = 'as' . ucfirst($query['page']);
		unset($query['page']);
	}
	
	return $segments;
}

function Rsgallery2ParseRoute($segments)
{
	$vars	= array();
	
	// Get the active menu item.
	$menu	= &JSite::getMenu();
	$item	= &$menu->getActive();

	if(!empty($item)){
		// We only want the gid from the menu-item-link when (this case the menulink refers to a subgallery)
		// - it is the only gid: e.g. no 'category' in $segments (it is not a subgallery of the gallery shown with the menu-item)
		// - we do not have id in the URL, e.g. no 'item' in $segments
		if (!in_array("gallery", $segments) AND !in_array("item", $segments) AND !in_array("category", $segments)) {	//'category' for links created with RSG2 version <= 2.1.1
			if(preg_match( "/gid=([0-9]*)/", $item->link, $matches) != 0){
				$vars['gid'] = $matches[1];
			}
		}
	}
	
	for ($index = 0 ; $index < count($segments) ; $index++){
		
		switch ($segments[$index]){
			// gallery link (subgallery of the gallery shown with the menu-item)
			case 'category':	//changed 'category' to 'gallery' after version 2.1.1
			case 'gallery':		
			{
				$vars['gid'] = Rsgallery2GetCategoryId($segments[++$index]);
				break;
			}
			// item link
			case 'item':
			{
				$vars['id']  = Rsgallery2GetItemId($segments[++$index]);
				break;
			}
			// gallery paging
			case 'categoryPage':
			{
				$vars['limitstartg'] = 	$segments[++$index];
				$vars['limitstart'] = 1;
				break;
			}
			// item paging
			case 'itemPage':
			{
				$vars['limitstart'] = 	$segments[++$index];
				break;
			}
			
		}
		// how to show the item
		$pos = strpos($segments[$index],'as'); 
		if($pos !== false && $pos == 0)
		{
			$vars['page'] = strtolower(substr($segments[$index],2));
		}
		
		
	}
	
	if(isset($vars["id"]) && !isset($vars['page']))
	{
		$vars['page'] = "inline";
	}
	return $vars;
}

/**
 * Converts a category Id to its SEF representation
 * 
 *  @param $categoyId int Numerial value of the category
 *	@return string String representation of the category
 * 
 **/
function Rsgallery2GetCategoryName($categoryId){
	
	global $config;
	
	Rsgallery2InitConfig();
	
	// fetch the gallery name from the database if advanced sef is active
	// else return the numerical value	
	if($config->get("advancedSef") == true)
	{
		$dbo = JFactory::getDBO();
		$query = "SELECT name FROM #__rsgallery2_galleries WHERE id=$categoryId";
		$dbo->setQuery($query);
		$result = $dbo->query();
		if($dbo->getNumRows($result) != 1){
			// gallery name was not unique or is unknown, use the numeric value instead.
			$segment = $categoryId;
		}
		else{			
			$segment = $dbo->loadResult($result);
		}
	}
	else{
		$segment = $categoryId;
	}
	
	return $segment;
}

/**
 * Converts a category SEF name to its id
 * 
 *  @param $categoyName mixed SEF name or id of the category
 *	@return int id of the category
 * 
 **/
function Rsgallery2GetCategoryId($categoyName){
	global $config;
	
	Rsgallery2InitConfig();
	
	// fetch the gallery id from the database if advanced sef is active
	if($config->get("advancedSef") == true)
	{
		$dbo = JFactory::getDBO();
		//Use getEscaped for when gallerynames have ' in them!
		$query = "SELECT id FROM #__rsgallery2_galleries WHERE name='".$dbo->getEscaped($categoyName)."'";
		$dbo->setQuery($query);
		$result = $dbo->query();

		if($dbo->getNumRows($result) != 1){
			// if the gallery name is not unique, tell the user and redirect to the root gallery
			//When using JoomFish the translation of an existing gallery may not
			// be found, so the $result is 0 rows, handle the error message:
			if($dbo->getNumRows($result) == 0){
				$msg = 'ROUTER_NO_GALLERY_FOUND';
			} else {
				$msg = "NON_UNIQUE_CAT";
			}
			$lang = JFactory::getLanguage();
			$lang->load("com_rsgallery2");
			JError::raiseWarning(0, JText::sprintf($msg, $categoyName));
			$id = 0;
		}
		else{			
			$id = $dbo->loadResult($result);
		}
	}
	else{
		$id = $categoyName;
	}
	return $id;
}

/**
 * Converts a item SEF name to its id
 * 
 *  @param $categoyName mixed SEF name or id of the category
 *	@return int id of the category
 * 
 **/
function Rsgallery2GetItemId($itemName){
	
	global $config;
	
	Rsgallery2InitConfig();
	
	// fetch the gallery id from the database if advanced sef is active
	if($config->get("advancedSef") == true)
	{
		$dbo = JFactory::getDBO();
		$query = "SELECT id FROM #__rsgallery2_files WHERE title='".$dbo->getEscaped($itemName)."'";
		$dbo->setQuery($query);
		$result = $dbo->query();

		if($dbo->getNumRows($result) != 1){
			// if the item name is not unique,  tell the user and redirect to the main page
			// Error message depend on number of results...
			if($dbo->getNumRows($result) == 0){
				$msg = 'ROUTER_NO_IMAGE_FOUND';
			} else {
				$msg = "NON_UNIQUE_ITEM";
			}
			global $mainframe;
			JFactory::getLanguage()->load("com_rsgallery2");
			$mainframe->redirect("index.php", JText::sprintf($msg, $itemName));
		}
		else{			
			$id = $dbo->loadResult($result);
		}
	}
	else{
		$id = $itemName;
	}
	
	return $id;
}

/**
 * Converts a item Id to its SEF representation
 * 
 *  @param $categoyId int Numerial value of the category
 *	@return string String representation of the category
 * 
 **/
function Rsgallery2GetItemName($itemId){
	
	global $config;
	
	Rsgallery2InitConfig();
	
	// fetch the gallery name from the database if advanced sef is active
	// else return the numerical value	
	if($config->get("advancedSef") == true)
	{
		$dbo = JFactory::getDBO();
		$query = "SELECT title FROM #__rsgallery2_files WHERE id=$itemId";
		$result = $dbo->query($query);
		
		$dbo->setQuery($query);
		$result = $dbo->query();
		if($dbo->getNumRows($result) != 1){
			// item name was not unique or is unknown, use the numeric value instead.
			$segment = $itemId;
		}
		else{			
			$segment = $dbo->loadResult($result);
		}
	}
	else{
		$segment = $itemId;
	}
	
	return $segment;
}
function Rsgallery2InitConfig()
{
	global $config;
	
	if($config == null){
		if (!defined('JPATH_RSGALLERY2_ADMIN')){
			define('JPATH_RSGALLERY2_ADMIN', JPATH_ROOT. DS .'administrator' . DS . 'components' . DS . 'com_rsgallery2');
		}
		require_once(JPATH_RSGALLERY2_ADMIN . DS . 'includes' . DS . 'config.class.php');
		$config = new rsgConfig();
	}
}
