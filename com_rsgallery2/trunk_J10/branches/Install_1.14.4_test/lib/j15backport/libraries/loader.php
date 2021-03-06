<?php
/**
* @version $Id: loader.php 8180 2007-07-23 05:52:29Z eddieajau $
* @package		Joomla.Framework
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

if(!defined('DS')) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

/**
 * @package		Joomla.Framework
 */
class JLoader
{
	 /**
	 * Loads a class from specified directories.
	 *
	 * @param string $name	The class name to look for ( dot notation ).
	 * @param string $base	Search this directory for the class.
	 * @param string $key	String used as a prefix to denote the full path of the file ( dot notation ).
	 * @return boolean True if the requested class has been successfully included
	 * @since 1.5
	 */
	function import( $filePath, $base = null, $key = null )
	{
		static $paths;

		if (!isset($paths))
		{
			$paths = array();
		}

		//$keyPath	= $key ? $key . $filePath : $filePath;
		if ( $key ) {
			$keyPath = $key . $filePath;
		} else {
			$keyPath = $filePath;
		}

		$trs	= 1;

		if (!isset($paths[$keyPath]))
		{
			$parts = explode( '.', $filePath );

			if ( ! $base ) {
				$base =  dirname( __FILE__ );
			}

			if(array_pop( $parts ) == '*')
			{
				$path = $base . DS . implode( DS, $parts );

				if (!is_dir( $path )) {
					return false;
				}

				$dir = dir( $path );
				while ($file = $dir->read()) {
					if (preg_match( '#(.*?)\.php$#', $file, $m )) {
						$nPath = str_replace( '*', $m[1], $filePath );
						$keyPath	= $key . $nPath;
						// we need to check each file again incase one has a jimport
						if (!isset($paths[$keyPath]))
						{
							$rs	= include($path . DS . $file);
							$paths[$keyPath] = $rs;
							$trs =& $rs;
						}
					}
				}
				$dir->close();
			} else {
				$path = str_replace( '.', DS, $filePath );

				/* for debugging missing files
				if( ! file_exists( $base . DS . $path . '.php' )){
					JError::raiseError( 1, 'Trying to import missing file: '.$base . DS . $path . '.php' );
				}
				*/

				$trs	= include($base . DS . $path . '.php');
			}

			$paths[$keyPath] = $trs;
		}
		return $trs;
	}
}

/**
 * Intelligent file importer
 *
 * @access public
 * @param string $path A dot syntax path
 * @since 1.5
 */
function jimport( $path ) {
	return JLoader::import($path, null, 'libraries.' );
}
