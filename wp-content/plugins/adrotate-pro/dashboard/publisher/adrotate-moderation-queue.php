<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>
<h3><?php _e('Waiting for Review and Approval', 'adrotate'); ?></h3>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-moderate">
	<?php wp_nonce_field('adrotate_bulk_ads_queue','adrotate_nonce'); ?>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="adrotate_queue_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate'); ?></option>
		        <option value="approve"><?php _e('Approve', 'adrotate'); ?></option>
		        <option value="reject"><?php _e('Reject', 'adrotate'); ?></option>
		        <option value="delete"><?php _e('Delete', 'adrotate'); ?></option>
			</select>
			<input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate'); ?>" class="button-secondary" />
		</div>
	
		<br class="clear" />
	</div>

	<table class="widefat" style="margin-top: .5em">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
			<th width="4%"><center><?php _e('ID', 'adrotate'); ?></center></th>
			<th width="12%"><?php _e('Show from', 'adrotate'); ?></th>
			<th width="12%"><?php _e('Show until', 'adrotate'); ?></th>
			<th>&nbsp;</th>
			<th width="20%"><?php _e('Advertiser', 'adrotate'); ?></th>
			<th width="5%"><center><?php _e('Weight', 'adrotate'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	if ($queued) {
		$class = $errorclass = '';
		foreach($queued as $queue) {			
			if($adrotate_debug['dashboard'] == true) {
				echo "<tr><td>&nbsp;</td><td><strong>[DEBUG]</strong></td><td colspan='5'><pre>";
				echo "Ad Specs: <pre>";
				print_r($queue); 
				echo "</pre></td></tr>"; 
			}
			$advertiser = $wpdb->get_var("SELECT `user` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$queue['id']."' AND `group` = 0;");
			$advertiser_name = $wpdb->get_var("SELECT `display_name` FROM `$wpdb->users` WHERE `ID` = $advertiser;");
			
			$groups	= $wpdb->get_results("
				SELECT 
					`".$wpdb->prefix."adrotate_groups`.`name` 
				FROM 
					`".$wpdb->prefix."adrotate_groups`, 
					`".$wpdb->prefix."adrotate_linkmeta` 
				WHERE 
					`".$wpdb->prefix."adrotate_linkmeta`.`ad` = '".$queue['id']."'
					AND `".$wpdb->prefix."adrotate_linkmeta`.`group` = `".$wpdb->prefix."adrotate_groups`.`id`
					AND `".$wpdb->prefix."adrotate_linkmeta`.`user` = 0
				;");
			$grouplist = '';
			foreach($groups as $group) {
				$grouplist .= $group->name.", ";
			}
			$grouplist = rtrim($grouplist, ", ");
			
			if($class != 'alternate') $class = 'alternate';
				else $class = '';
			if($queue['lastactive'] <= $in7days) $errorclass = ' row_error'; 
			?>
		    <tr id='adrotateindex' class='<?php echo $class.$errorclass; ?>'>
				<th class="check-column"><input type="checkbox" name="queuecheck[]" value="<?php echo $queue['id']; ?>" /></th>
				<td><center><?php echo $queue['id'];?></center></td>
				<td><?php echo date_i18n("F d, Y", $queue['firstactive']);?></td>
				<td><span style="color: <?php echo adrotate_prepare_color($queue['lastactive']);?>;"><?php echo date_i18n("F d, Y", $queue['lastactive']);?></span></td>
				<td><strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=edit&ad='.$queue['id']);?>" title="<?php _e('Edit', 'adrotate'); ?>"><?php echo stripslashes(html_entity_decode($queue['title']));?></a></strong><?php if($groups) echo '<br /><em style="color:#999">'.$grouplist.'</em>'; ?></td>
				<td><?php echo $advertiser_name; ?></td>
				<td><center><?php echo $queue['weight']; ?></center></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-groups'>
			<th class="check-column">&nbsp;</th>
			<td colspan="10"><em><?php _e('No ads in queue yet!', 'adrotate'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
</form>