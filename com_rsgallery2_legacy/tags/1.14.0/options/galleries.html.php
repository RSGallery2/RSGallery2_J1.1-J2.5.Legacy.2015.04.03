<?php
/**
* Galleries option for RSGallery2 - HTML display code
* @version $Id$
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Explain what this class does
 * @package RSGallery2
 */
class html_rsg2_galleries{
    /**
     * show list of galleries
     */
    function show( &$rows, &$lists, &$search, &$pageNav ){
        global $my, $rsgOption, $option;

        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
        <table class="adminheading">
        <tr>
            <th>
            <?php echo _RSGALLERY_GAL_MANAGE?>
            </th>
            <td nowrap="true">
            <?php echo _RSGALLERY_GAL_MAX_LEVELS?>
            </td>
            <td>
            <?php echo $lists['levellist'];?>
            </td>
            <td>
            <?php echo _RSGALLERY_GAL_FILTER?>:
            </td>
            <td>
            <input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
            </td>
        </tr>
        </table>

        <table class="adminlist">
        <tr>
            <th width="5">
            ID
            </th>
            <th width="20">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
            </th>
            <th class="Name">
            <?php echo _RSGALLERY_GAL_NAME?>
            </th>
            <th width="5%">
            <?php echo _RSGALLERY_GAL_PUBLISHED?>
            </th>
            <th colspan="2" width="5%">
            <?php echo _RSGALLERY_GAL_REORDER?>
            </th>
			<th width="2%"><?php echo _RSGALLERY_GAL_ORDER?></th>
			<th width="2%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )">
				<img src="images/filesave.png" width="16" height="16" alt="Save Order" />
			</a>
			</th>
			<th width="4%"><?php echo _RSGALLERY_GAL_COUNT?></th>
            <th width="25%">
            <?php echo _RSGALLERY_GAL_HITS?>
            </th>
        </tr>
        <?php
        $k = 0;
        for ($i=0, $n=count( $rows ); $i < $n; $i++) {
            $row = &$rows[$i];

            $link   = "index2.php?option=$option&rsgOption=$rsgOption&task=editA&hidemainmenu=1&id=". $row->id;

            $task   = $row->published ? 'unpublish' : 'publish';
            $img    = $row->published ? 'publish_g.png' : 'publish_x.png';
            $alt    = $row->published ? 'Published' : 'Unpublished';

            $checked    = mosCommonHTML::CheckedOutProcessing( $row, $i );
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
                    echo $row->name;
                } else {
                    ?>
                    <a href="<?php echo $link; ?>" name="Edit Gallery">
                    <?php echo $row->treename;; ?>
                    </a>
                    <?php
                }
                ?>
                </td>
                <td align="center">
                <a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
                <img src="images/<?php echo $img;?>" width="12" height="12"  alt="<?php echo $alt; ?>" />
                </a>
                </td>
                <td>
                <?php echo $pageNav->orderUpIcon( $i ); ?>
                </td>
                <td>
                <?php echo $pageNav->orderDownIcon( $i, $n ); ?>
                </td>
                <td colspan="2" align="center">
                <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
                </td>
                <td align="center">
                <?php $gallery = rsgGalleryManager::get( $row->id ); echo $gallery->itemCount()?>
                </td>
                <td align="left">
                <?php echo $row->hits; ?>
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
        <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }

    /**
     * warns user what will be deleted
     */
    function removeWarn( $galleries ){
        global $rsgOption, $option;
        ?>
        <form action="index2.php" method="post" name="adminForm">
        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0" />

<!--         these are the galleries the user has chosen to delete: -->
        <?php foreach( $galleries as $g ): ?>
            <input type="hidden" name="cid[]" value="<?php echo $g->get('id'); ?>" />
        <?php endforeach; ?>
        
        <h2>The following will be deleted:</h2>
        <div style='text-align: left;' >

        <?php html_rsg2_galleries::printTree( $galleries ); ?>
        
        </div>
        </form>
        <?php
    }
    function printTree( $galleries ){
        echo "<ul>";

        foreach( $galleries as $g ){
            // print gallery details
            echo "<li>". $g->get('name') ." (". count($g->itemRows()) ." images)";
            html_rsg2_galleries::printTree( $g->kids() );
            echo "</li>";
        }
        echo "</ul>";
    }

	/**
	* Writes the edit form for new and existing record
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param rsgGallery The gallery object
	* @param array An array of select lists
	* @param object Parameters
	* @param string The option
	*/
	function edit( &$row, &$lists, &$params, $option ) {
		global $rsgOption, $rsgAccess, $my, $rsgConfig;
		mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );
	
		$task = rsgInstance::getVar( 'task'  , '');
	
		mosCommonHTML::loadOverlib();
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
	
			// do field validation
			if (form.name.value == ""){
				alert( "Gallery must have a name" );
			} else {
				<?php getEditorContents( 'editor1', 'description' ) ; ?>
				submitform( pressbutton );
			}
		}
	
		function selectAll() {
			if(document.adminForm.checkbox0.checked) {
				for (i = 0; i < 10; i++) {
					document.getElementById('p' + i).checked=true;
				}
			} else {
				for (i = 0; i < 10; i++) {
					document.getElementById('p' + i).checked=false;
				}
			}
		}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo _RSGALLERY_GAL_GAL?>:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
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
					<?php echo _RSGALLERY_GAL_DETAILS?>
					</th>
				</tr>
				<tr>
					<td width="20%" align="right">
					<?php echo _RSGALLERY_GAL_NAME?>:
					</td>
					<td width="80%">
					<input class="text_area" type="text" name="name" size="50" maxlength="250" value="<?php echo $row->name;?>" />
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo _RSGALLERY_GAL_OWNER;?>:
					</td>
					<td>
					<?php echo $lists['uid']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _RSGALLERY_GAL_DESCR?>:
					</td>
					<td>
					<?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
					editorArea( 'editor1',  $row->description , 'description', '100%;', '300', '10', '20' ) ; ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo _RSGALLERY_GAL_PARENT;?>:
					</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _RSGALLERY_GAL_THUMB;?>:
					</td>
					<td>
					<?php echo imgUtils::showThumbNames($row->id, $row->thumb_id); ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _RSGALLERY_GAL_ORDERING;?>:
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo _RSGALLERY_GAL_PUBLISHED;?>:
					</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				</table>
			</td>
			<td width="40%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="1">
					<?php echo _RSGALLERY_GAL_PARAMETERS;?>
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render();?>
					</td>
				</tr>
				</table><br/>
				<table class="adminform">
				<?php
				if ($rsgConfig->get('acl_enabled')) {
					?>
					<tr>
						<th colspan="1"><?php echo _RSGALLERY_GAL_PERMS?></th>
					</tr>	                
					<?php
					if ( !isset($row->id) ) {
					?>
	
					<tr>
						<td><?php echo _RSGALLERY_GAL_DEF_PERM_CREATE?></td>
					</tr>
					<?php
					} else {
						$perms = $rsgAccess->returnPermissions($row->id);
	
						if ( !$perms ) {
							?>
							<tr>
								<td colspan="6"><?php echo _RSGALLERY_GAL_NO_PERM_FOUND?></td>
							</tr>
							<?php	
						} else {
							?>
							<tr>
								<td>
								<table class="adminform" border="0" width="100%">
								<tr>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_USERTYPE?></span></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_VIEW_GAL?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_UPL_EDIT_IMG?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_DEL_IMG?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_MOD_GAL?></td>
									<td valign="top" width="50"><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_DEL_GAL?></td>
								</tr>
								<tr>
									<td><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_ACL_PUB?></td>
									<td><input id="p0" type="checkbox" name="perm[0]" value="1" <?php if ($perms->public_view == 1) echo "CHECKED";?>></td>
									<td><input id="p1" type="checkbox" name="perm[1]" value="1" <?php if ($perms->public_up_mod_img == 1) echo "CHECKED";?>></td>
									<td><input id="p2" type="checkbox" name="perm[2]" value="1" <?php if ($perms->public_del_img == 1) echo "CHECKED";?>></td>
									<td><input id="p3" type="checkbox" name="perm[3]" value="1" <?php if ($perms->public_create_mod_gal == 1) echo "CHECKED";?>></td>
									<td><input id="p4" type="checkbox" name="perm[4]" value="1" <?php if ($perms->public_del_gal == 1) echo "CHECKED";?>></td>
								</tr>
								<tr>
									<td><span style="font-weight:bold;"><?php echo _RSGALLERY_GAL_ACL_REG?></td>
									<td><input id="p5" type="checkbox" name="perm[5]" value="1" <?php if ($perms->registered_view == 1) echo "CHECKED";?>></td>
									<td><input id="p6" type="checkbox" name="perm[6]" value="1" <?php if ($perms->registered_up_mod_img == 1) echo "CHECKED";?>></td>
									<td><input id="p7" type="checkbox" name="perm[7]" value="1" <?php if ($perms->registered_del_img == 1) echo "CHECKED";?>></td>
									<td><input id="p8" type="checkbox" name="perm[8]" value="1" <?php if ($perms->registered_create_mod_gal == 1) echo "CHECKED";?>></td>
									<td><input id="p9" type="checkbox" name="perm[9]" value="1" <?php if ($perms->registered_del_gal == 1) echo "CHECKED";?>></td>
								</tr>
								<tr>
									<td colspan="6"><input type="checkbox" name="checkbox0" value="true" onClick='selectAll()'><?php echo _RSGALLERY_GAL_SEL_DESEL_ALL?></td>
								</tr>
								</table>
								</td>
							</tr>
							<?php
						}
					}
				}
				?>
				</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="rsgOption" value="<?php echo $rsgOption;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}