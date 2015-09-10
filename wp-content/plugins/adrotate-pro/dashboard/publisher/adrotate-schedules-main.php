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
<h3><?php _e('Manage Schedules', 'adrotate'); ?></h3>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-schedules">
	<?php wp_nonce_field('adrotate_bulk_schedules','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate'); ?></option>
		        <option value="schedule_delete"><?php _e('Delete', 'adrotate'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate'); ?>" class="button-secondary" />
		</div>	
		<br class="clear" />
	</div>

	<table class="widefat" style="margin-top: .5em">
		<thead>
		<tr>
			<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
			<th width="4%"><center><?php _e('ID', 'adrotate'); ?></center></th>
			<th width="17%"><?php _e('Start', 'adrotate'); ?> / <?php _e('End', 'adrotate'); ?></th>
	        <th width="4%"><center><?php _e('Ads', 'adrotate'); ?></center></th>
			<th>&nbsp;</th>
	        <th width="15%"><center><?php _e('Max Clicks', 'adrotate'); ?></center></th>
	        <th width="15%"><center><?php _e('Max Impressions', 'adrotate'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	$schedules = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."adrotate_schedule` WHERE `name` != '' ORDER BY `id` ASC;");
	if($schedules) {
		$class = '';
		foreach($schedules as $schedule) {
			$schedulesmeta = $wpdb->get_results("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `user` = 0 AND `schedule` = ".$schedule->id.";");
			$ads_use_schedule = '';
			if($schedulesmeta) {
				foreach($schedulesmeta as $meta) {
					$ads_use_schedule[] = $meta->ad;
					unset($meta);
				}
			}
			if($schedule->maxclicks == 0) $schedule->maxclicks = __('unlimited', 'adrotate');
			if($schedule->maximpressions == 0) $schedule->maximpressions = __('unlimited', 'adrotate');

			($class != 'alternate') ? $class = 'alternate' : $class = '';
			if($schedule->stoptime < $in2days) $class = 'row_urgent';
			if($schedule->stoptime < $now) $class = 'row_inactive';
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="schedulecheck[]" value="<?php echo $schedule->id; ?>" /></th>
				<td><center><?php echo $schedule->id;?></center></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule->starttime);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule->stoptime);?>;"><?php echo date_i18n("F d, Y H:i", $schedule->stoptime);?></span></td>
		        <td><center><?php echo count($schedulesmeta); ?></center></td>
				<td><a href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=edit&schedule='.$schedule->id);?>"><?php echo stripslashes(html_entity_decode($schedule->name)); ?></a><?php if($schedule->spread == 'Y') { ?><span style="color:#999;"><br /><span style="font-weight:bold;"><?php _e('Spread:', 'adrotate'); ?></span> Max. <?php echo $schedule->dayimpressions; ?> <?php _e('impressions per day', 'adrotate'); ?></span><?php } ?></td>
		        <td><center><?php echo $schedule->maxclicks; ?></center></td>
		        <td><center><?php echo $schedule->maximpressions; ?></center></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-schedules'>
			<th class="check-column">&nbsp;</th>
			<td colspan="7"><em><?php _e('No schedules created yet!', 'adrotate'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon.", "adrotate"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #466f82; height: 12px; width: 12px; background-color: #8dcede">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has expired.", "adrotate"); ?>
</center></p>
</form>
