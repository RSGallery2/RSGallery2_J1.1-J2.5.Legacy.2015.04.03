<?php
/**
* Images option for RSGallery2 - HTML display code
* @version $Id$
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
 * Handles HTML screens for image option 
 * @package RSGallery2
 */
class html_rsg2_images {

	function showImages( $option, &$rows, &$lists, &$search, &$pageNav ) {
		global $my, $rsgOption, $option, $rsgConfig, $mosConfig_live_site;
		?>
 		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th><?php echo _RSGALLERY_IMG_IMG_MANAGE?></th>
			<td><?php echo _RSGALLERY_IMG_CPY_MV_GAL?></td>
			<td><?php echo $lists['move_id'];?></td>
			<td>&nbsp;&nbsp;</td>
			<td><?php echo _RSGALLERY_IMG_FILTER?></td>
			
			<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
			<td align="right"><?php echo $lists['gallery_id'];?></td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="5">ID</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title"><?php echo _RSGALLERY_IMG_TITLE?></th>
			<th width="5%"><?php echo _RSGALLERY_MY_IMAGES_PUBLISHED?></th>
			<th colspan="2" width="5%"><?php echo _RSGALLERY_REORDER?></th>
			<th width="2%"><?php echo _RSGALLERY_IMG_ORDER?></th>
			<th width="2%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )">
				<img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" />
			</a>
			</th>
			<th width="15%" align="left"><?php echo _RSGALLERY_GAL_GAL?></th>
			<th width="5%"><?php echo _RSGALLERY_IMAGEHITS?></th>
			<th width=""><?php echo _RSGALLERY_IMG_DATE_TIME?></th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_rsgallery2&rsgOption='.$rsgOption.'&task=editA&hidemainmenu=1&id='. $row->id;

			$task 	= $row->published ? 'unpublish' : 'publish';
			$img 	= $row->published ? 'publish_g.png' : 'publish_x.png';
			$alt 	= $row->published ? 'Published' : 'Unpublished';

			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );

			$row->cat_link 	= 'index2.php?option=com_rsgallery2&rsgOption=galleries&task=editA&hidemainmenu=1&id='. $row->gallery_id;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $row->id; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td>
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->title;
				} else {
					$gallery = rsgGalleryManager::getGalleryByItemID($row->id);
					if($gallery !== null){
						if (is_a( $gallery->getItem($row->id), 'rsgItem_audio' ) ) {
							$type = 'audio';
						} else {
							$type = 'image';
						}
					}
					echo JHTML::tooltip('<img src="'.$mosConfig_live_site.$rsgConfig->get('imgPath_thumb').'/'.$row->name.'.jpg" alt="'.$row->name.'" />',
					 _RSGALLERY_IMG_EDIT_IMG,
					 $row->name,
					 $row->title.'&nbsp;('.$row->name.')',
					 $link,
					1);
				}
				?>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
				<td>
				<?php echo $pageNav->orderUpIcon( $i, ($row->gallery_id == @$rows[$i-1]->gallery_id) ); ?>
				</td>
	  			<td>
				<?php echo $pageNav->orderDownIcon( $i, $n, ($row->gallery_id == @$rows[$i+1]->gallery_id) ); ?>
				</td>
				<td colspan="2" align="center">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td>
				<a href="<?php echo $row->cat_link; ?>" title="Edit Category">
				<?php echo $row->category; ?>
				</a>
				</td>
				<td align="left">
				<?php echo $row->hits; ?>
				</td>
				<td align="left">
				<?php echo $row->date;?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
		<?php
	}

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosWeblink The weblink object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
	function editImage( &$row, &$lists, &$params, $option ) {
		global $rsgOption, $mosConfig_live_site;
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'descr' );
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.title.value == ""){
				alert( "Image must have a title" );
			} else if (form.gallery_id.value == "0"){
				alert( "You must select a category." );
			} else {
				<?php getEditorContents( 'editor1', 'descr' ) ; ?>
				submitform( pressbutton );
			}
		}
		</script>
		
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th><?php echo _RSGALLERY_IMG_IMAGE?>:<small><?php echo $row->id ? 'Edit' : 'New';?></small></th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="2"><?php echo _RSGALLERY_IMG_DETAILS?></th>
				</tr>
				<tr>
					<td width="20%" align="right"><?php echo _RSGALLERY_IMAGENAME?>:</td>
					<td width="80%">
						<input class="text_area" type="text" name="title" size="50" maxlength="250" value="<?php echo $row->title;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" align="right"><?php echo _RSGALLERY_IMAGEFILE?>:</td>
					<td width="80%"><?php echo $row->name;?></td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _RSGALLERY_IMAGECAT?>:</td>
					<td><?php echo $lists['gallery_id']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _RSGALLERY_DESCR?>:</td>
					<td>
						<?php
						// parameters : areaname, content, hidden field, width, height, rows, cols
                    	editorArea( 'editor1',  $row->descr , 'descr', '100%;', '200', '10', '20' ) ; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _RSGALLERY_IMG_ORDERING?>:</td>
					<td><?php echo $lists['ordering']; ?></td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo _RSGALLERY_MY_IMAGES_PUBLISHED?>:</td>
					<td><?php echo $lists['published']; ?></td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="1"><?php echo _RSGALLERY_IMG_IMG_PREV?></th>
				</tr>
				<tr>
					<td>
						<div align="center">
						<?php
						$gallery = rsgGalleryManager::getGalleryByItemID($row->id);
						$item = $gallery->getItem( $row->id );
						
						$original	= $item->original();
						if (is_a( $item, 'rsgItem_audio' ) ) {
							?>
							<object type="application/x-shockwave-flash" width="400" height="15" data="<?php echo $mosConfig_live_site ?>/components/com_rsgallery2/flash/xspf/xspf_player_slim.swf?song_title=<?php echo $row->name?>&song_url=<?php echo audioUtils::getAudio($row->name)?>"><param name="movie" value="<?php echo $mosConfig_live_site ?>/components/com_rsgallery2/flash/xspf/xspf_player_slim.swf?song_title=<?php echo $item->title;?>&song_url=<?php echo $original->url();?>" /></object>
							<?php
						} else {
							$thumb 		= $item->thumb();
							$display	= $item->display();
							?>
							<img width="300" border="1" src="<?php echo $display->url() ?>" alt="<?php echo htmlspecialchars( stripslashes( $item->descr ), ENT_QUOTES );?>" />
							<br />
							<?php
						}
						?>
						</div>
					</td>
				</tr>
				</table>
				<table class="adminform">
				<tr>
					<th colspan="1"><?php echo _RSGALLERY_IMG_PARAMETERS?></th>
				</tr>
				<tr>
					<td><?php echo $params->render();?>&nbsp;</td>
				</tr>
				</table>
				<table class="adminform">
				<tr>
					<th colspan="1"><?php echo _RSGALLERY_IMG_LINKS?></th>
				</tr>
				<tr>
					<td>
						<table width="100%" class="imagelist">
						<?php if (!is_a( $item, 'rsgItem_audio' ) ) {?>
						<tr>
							<td width="40%" align="right" valign="top"> <a href="<?php echo $thumb->url();?>" target="_blank" alt="<?php echo $item->descr;?>">Thumb</a>:</td>
							<td><input type="text" name="thumb_url" class="text_area" size="" value="<?php echo $thumb->url();?>" /></td>
						</tr>
						<tr>
							<td width="40%" align="right" valign="top"><a href="<?php echo $display->url();?>" target="_blank" alt="<?php echo $item->descr;?>">Display</a>:</td>
							<td ><input type="text" name="display_url" class="text_area" size="" value="<?php echo $display->url();?>" /></td>
						</tr>
						<?php }?>
						<tr>
							<td width="40%" align="right" valign="top"><a href="<?php echo $original->url();?>" target="_blank" alt="<?php echo $item->descr;?>">Original</a>:</td>
							<td><input type="text" name="original_url" class="text_area" size="" value="<?php echo $original->url();?>" /></td>
						</td>
					</tr>
					</table>		
				</td>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="name" value="<?php echo $row->name; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosWeblink The weblink object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
	function uploadImage( $lists, $option ) {
		global $rsgOption, $mosConfig_live_site;
		//mosMakeHtmlSafe( $row, ENT_QUOTES, 'descr' );
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
        
			// do field validation
			if (form.gallery_id.value == "0"){
				alert( "<?php echo _RSGALLERY_IMG_SELECT_GAL?>" );
			} else if (form.images.value == ''){
				alert( "<?php echo _RSGALLERY_IMG_NO_FILE_SELECT?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<?php 
		//translated text into javascript -> javascript to .php file
		/*<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/administrator/components/com_rsgallery2/includes/script.php"></script>*/
		require_once(RSG2_PATH_ADMIN . DS . 'includes' . DS . 'script.php');
		?>
		<form action="index2.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo _RSGALLERY_IMG_IMAGE?>:
			<small>
			<?php echo _RSGALLERY_IMG_UPLOAD?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="2">
					<?php echo _RSGALLERY_IMG_UPL_DETAILS?>
					</th>
				</tr>
				<tr>
					<td width="20%" align="right"></td>
					<td width="80%"><?php echo $lists['gallery_id']?></td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _RSGALLERY_IMG_GEN_DESCR?>:
					</td>
					<td>
					<textarea class="inputbox" cols="50" rows="5" name="descr" style="width:500px" width="500"></textarea>
					</td>
				</tr>
				</table>
				<br />
				<table class="adminform">
				<tr>
					<th colspan="2">
					<?php echo _RSGALLERY_IMG_IMG_FILES?>
					</th>
				</tr>
				<tr>
					<td  width="20%" valign="top" align="right">
					<?php echo _RSGALLERY_IMG_IMAGES?>:
					</td>
					<td width="80%">
						<?php echo _RSGALLERY_TITLE?>:&nbsp;<input class="text" type="text" id= "title" name="title[]" value="" size="60" maxlength="250" /><br /><br />
						<?php echo _RSGALLERY_IMG_FILE?>:&nbsp;&nbsp;<input type="file" size="48" id="images" name="images[]" /><br /><hr />
    					<span id="moreAttachments"></span>
    					<a href="javascript:addAttachment(); void(0);"><?php echo _RSGALLERY_IMG_MORE?></a><br />
    					<noscript><input type="file" size="48" name="images[]" /><br /></noscript>
					</td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="1">
					<?php echo _RSGALLERY_IMG_PARAMETERS?>
					</th>
				</tr>
				<tr>
					<td>
					<?php /*echo $params->render();*/?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
	
	function batchupload($option) {
        global $rsgConfig, $task, $rsgOption;
        $FTP_path = $rsgConfig->get('ftp_path');
        $size = round( ini_get('upload_max_filesize') * 1.024 );
        ?>
        <script language="javascript" type="text/javascript">
        <!--
        function submitbutton(pressbutton) {
            var form = document.adminForm;
 
            for (i=0;i<document.forms[0].batchmethod.length;i++) {
                if (document.forms[0].batchmethod[i].checked) {
                    upload_method = document.forms[0].batchmethod[i].value;
                    }
            }
            
            for (i=0;i<document.forms[0].selcat.length;i++) {
                if (document.forms[0].selcat[i].checked) {
                    selcat_method = document.forms[0].selcat[i].value;
                    }
            }
        if (pressbutton == 'controlPanel') {
        	location = "index2.php?option=com_rsgallery2";
        	return;
        }
        if (pressbutton == 'batchupload')
            {
            // do field validation
            if (upload_method == 'zip')
                {
                if (form.zip_file.value == '')
                    {
                    alert( "<?php echo _RSGALLERY_BATCH_NO_ZIP;?>" );
                    }        
               else if (form.xcat.value == '0' & selcat_method == '1')
                    {
                    alert("<?php echo _RSGALLERY_BATCH_GAL_FIRST;?>");
                    }
                else
                    {
                    form.submit();
                    }
                }
            else if (upload_method == 'ftp')
            	{
            	if (form.ftppath.value == '')
            		{
            		alert( "<?php echo _RSGALLERY_BATCH_NO_FTP;?>" );	
            		}
            	else if (form.xcat.value == '0' & selcat_method == '1')
            		{
					alert("<?php echo _RSGALLERY_BATCH_GAL_FIRST;?>");
            		}
            	else
            		{
            		form.submit();
            		}
            	}
            }
        }
        //-->
        </script>
        <form name="adminForm" action="index2.php" method="post" enctype="multipart/form-data">
        <table width="100%">
        <tr>
            <td width="300">&nbsp;</td>
            <td>
                <table class="adminform">
                <tr>
                    <th colspan="3"><font size="4"><?php echo _RSGALLERY_BATCH_STEP1;?></font></th>
                </tr>
                <tr>
                    <td width="200"><strong><?php echo _RSGALLERY_BATCH_METHOD;?></strong>
                    <?php
                    echo mosToolTip( _RSGALLERY_BATCH_METHOD_TIP, _RSGALLERY_BATCH_METHOD );
                    ?>
                    </td>
                    <td width="200">
                        <input type="radio" value="zip" name="batchmethod" CHECKED/>
                        <?php echo _RSGALLERY_BATCH_ZIPFILE; ?> :</td>
                    <td>
                        <input type="file" name="zip_file" size="20" />
                        <div style=color:#FF0000;font-weight:bold;font-size:smaller;>
                        <?php echo _RSGALLERY_BATCH_UPLOAD_LIMIT . $size ._RSGALLERY_BATCH_IN_PHPINI;?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="radio" value="ftp" name="batchmethod" />
                        <?php echo _RSGALLERY_BATCH_FTP_PATH;?> :</td>
                    <td>

                        <input type="text" name="ftppath" value="<?php echo $FTP_path; ?>" size="30" /><?php echo mosToolTip( _RSGALLERY_BATCH_FTP_PATH_OVERL, _RSGALLERY_BATCH_FTP_PATH );//echo _RSGALLERY_BATCH_DONT_FORGET_SLASH;?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;<br /></td>
                </tr>
                <tr>
                <td valign="top"><strong><?php echo _RSGALLERY_BATCH_CATEGORY;?></strong></td>
                    <td valign="top">
                        <input type="radio" name="selcat" value="1" CHECKED/>&nbsp;&nbsp;<?php echo _RSGALLERY_BATCH_YES_IMAGES_IN;?>:&nbsp;
                    </td>
                    <td valign="top">
                        <?php echo galleryUtils::galleriesSelectList( null, 'xcat', false );?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2"><input type="radio" name="selcat" value="0" />&nbsp;&nbsp;<?php echo _RSGALLERY_BATCH_NO_SPECIFY; ?></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;<br /></td>
                </tr>
                <tr class="row1">
                    <th colspan="3">
                        <div align="center" style="visibility: hidden;">
                        <input type="button" name="something" value="<?php echo _RSGALLERY_BATCH_NEXT;?>" onClick="submitbutton('batchupload');" />
                        </div>
                        </th>
                </tr>
                </table>
            </td>
            <td width="300">&nbsp;</td>
        </tr>
        </table>
        <input type="hidden" name="uploaded" value="1" />
        <input type="hidden" name="option" value="com_rsgallery2" />
        <input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
        <input type="hidden" name="task" value="batchupload" />
        <input type="hidden" name="boxchecked" value="0" />
        </form>
        <?php
        }

	function batchupload_2( $ziplist, $extractDir ){
        global $mosConfig_live_site, $database, $rsgOption;
        
        //Get variables from form
        $selcat 		= rsgInstance::getInt('selcat'  , null);
        $ftppath 		= rsgInstance::getVar('ftppath'  , null);
        $xcat 			= rsgInstance::getInt('xcat'  , null);
        $batchmethod 	= rsgInstance::getVar('batchmethod'  , null);
        ?>
        <form action="index2.php" method="post" name="adminForm">
        <table class="adminform">
        <tr>
            <th colspan="5" class="sectionname"><font size="4"><?php echo _RSGALLERY_BATCH_STEP2;?></font></th>
        </tr>
        <tr>
        <?php
		
        // Initialize k (the column reference) to zero.
        $k = 0;
        $i = 0;

        foreach ($ziplist as $filename) {
        	$k++;
        	//Check if filename is dir
        	if ( is_dir(JPATH_ROOT. DS . 'media' . DS . $extractDir . DS . $filename) ) {
        		continue;
        	} else {
        		//Check if file is allowed
        		$allowed_ext = array("gif","jpg","png");
        		$ext = fileHandler::getImageType( JPATH_ROOT. DS . 'media' . DS . $extractDir . DS . $filename );
        		if ( !in_array($ext, $allowed_ext) ) {
        			continue;
        		} else {
        			$i++;
        		}
        	}
            ?>
            <td align="center" valign="top" bgcolor="#CCCCCC">
                <table class="adminform" border="0" cellspacing="1" cellpadding="1">
                    <tr>
                        <th colspan="2">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="2" align="right"><?php echo _RSGALLERY_BATCH_DELETE;?> #<?php echo $i - 1;?>: <input type="checkbox" name="delete[<?php echo $i - 1;?>]" value="true" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2"><img src="<?php echo $mosConfig_live_site . "/media/" . $extractDir . "/" . $filename;?>" alt="" border="1" width="100" align="center" /></td>
                    </tr>
                    <input type="hidden" value="<?php echo $filename;?>" name="filename[]" />
                    <tr>
                        <td><?php echo _RSGALLERY_BATCH_TITLE;?></td>
                        <td>
                            <input type="text" name="ptitle[]" size="15" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _RSGALLERY_BATCH_GAL;?></td>
                        <td><?php
                            if ($selcat == 1 && $xcat !== '0')
                                {
                                ?>
                                <input type="text" name="cat_text" value="<?php echo htmlspecialchars(stripslashes(galleryUtils::getCatnameFromId($xcat)));?>" readonly />
                                <input type="hidden" name="category[]" value="<?php echo $xcat;?>" />
                                <?php
                                }
                            else
                                {
								echo galleryUtils::galleriesSelectList( null, 'category[]', false );
                                }
                                ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _RSGALLERY_DESCR;?></td>
                        <td><textarea cols="15" rows="2" name="descr[]"></textarea></td>
                    </tr>
                </table>
            </td>
            <?php
            if ($k == 5)
                {
                echo "</tr><tr>";
                $k = 0;
                }
            }
            ?>
			</table>

			<input type="hidden" name="teller" value="<?php echo $i;?>" />
			<input type="hidden" name="extractdir" value="<?php echo $extractDir;?>" />
			<input type="hidden" name="option" value="com_rsgallery2" />
			<input type="hidden" name="rsgOption" value="images" />
			<input type="hidden" name="task" value="save_batchupload" />

			</form>
        <?php
	}
}
?>
