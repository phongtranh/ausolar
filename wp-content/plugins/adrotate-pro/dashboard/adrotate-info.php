<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$banners = $groups = $schedules = $queued = 0;
$banners = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate` WHERE `type` != 'empty' AND `type` != 'a_empty';");
$groups = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate_groups` WHERE `name` != '';");
$schedules = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate_schedule` WHERE `name` != '';");
$queued = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate` WHERE `type` = 'queue';");
$data = get_option("adrotate_advert_status");
?>

<?php if($status > 0) adrotate_status($status, array('ticket' => $ticketid)); ?>

<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">

		<div id="postbox-container-1" class="postbox-container" style="width:50%;">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				
				<h3><?php _e('Currently', 'adrotate'); ?></h3>
				<div class="postbox-adrotate">
					<div class="inside">
						<table width="100%">
							<thead>
							<tr class="first">
								<td width="50%"><strong><?php _e('Your setup', 'adrotate'); ?></strong></td>
								<td width="50%"><strong><?php _e('Adverts that need you', 'adrotate'); ?></strong></td>
							</tr>
							</thead>
							
							<tbody>
							<tr class="first">
								<td class="first b"><a href="admin.php?page=adrotate-ads"><?php echo $banners; ?> <?php _e('Adverts', 'adrotate'); ?></a></td>
								<td class="b"><a href="admin.php?page=adrotate-ads"><?php echo $data['expiressoon']; ?> <?php _e('(Almost) Expired', 'adrotate'); ?></a></td>
							</tr>
							<tr>
								<td class="first b"><a href="admin.php?page=adrotate-groups"><?php echo $groups; ?> <?php _e('Groups', 'adrotate'); ?></a></td>
								<td class="b"><a href="admin.php?page=adrotate-ads"><?php echo $data['error']; ?> <?php _e('Have errors', 'adrotate'); ?></a></td>
							</tr>
							<tr>
								<td class="first b"><a href="admin.php?page=adrotate-schedules"><?php echo $schedules; ?> <?php _e('Schedules', 'adrotate'); ?></a></td>
								<td class="b"><a href="admin.php?page=adrotate-moderate"><?php echo $queued; ?> <?php _e('Queued', 'adrotate'); ?></a></td>
							</tr>
							</tbody>

							<thead>
							<tr class="first">
								<td colspan="2"><strong><?php _e('The last few days', 'adrotate'); ?> (<a href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=fullreport');?>"><?php _e('Full report', 'adrotate'); ?></a>)</strong></td>
							</tr>
							</thead>

							<tbody>
							<tr class="first">
								<td colspan="2">
						        	<?php
						        	$adstats = $wpdb->get_results("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` GROUP BY `thetime` DESC LIMIT 5;");
						
									if($adstats) {
										$adstats = array_reverse($adstats);
										$dates = $clicks = $impressions = 0;
						
										foreach($adstats as $result) {
											if($result->clicks == null) $result->clicks = '0';
											if($result->impressions == null) $result->impressions = '0';
											
											$dates .= ',"'.date_i18n("d M", $result->thetime).'"';
											$clicks .= ','.$result->clicks;
											$impressions .= ','.$result->impressions;
										}
						
										$dates = trim($dates, ",");
										$clicks = trim($clicks, ",");
										$impressions = trim($impressions, ",");
										
										echo '<div id="chart-1" style="height:150px; width:100%;"></div>';
										adrotate_draw_graph(1, $dates, $clicks, $impressions);
									} else {
										_e('No data to show!', 'adrotate');
									} 
									?>
								</td>
							</tr>
							</tbody>

							<thead>
							<tr class="first">
								<td colspan="2"><strong><?php _e('Support AdRotate', 'adrotate'); ?></strong></td>
							</tr>
							</thead>

							<tbody>
							<tr class="first">
								<td colspan="2">
									<p><center><?php _e('Consider writing a review if you like AdRotate. Thank you!', 'adrotate'); ?></center></p>
									<p><center><a class="button-secondary" target="_blank" href="https://wordpress.org/support/view/plugin-reviews/adrotate?rate=5#postform">Write review on WordPress.org</a></center></p>
								</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>

				<h3><?php _e('Ticket Support', 'adrotate'); ?></h3>
				<div class="postbox-adrotate">
					<div class="inside">
					<?php
					if(function_exists('json_encode')) {
						if($a['status'] == 1) { ?>					
							<form name="request" id="post" method="post" action="admin.php?page=adrotate">
								<?php wp_nonce_field('adrotate_nonce_support_request','adrotate_nonce_support'); ?>
							
								<p>&raquo; <?php _e('What went wrong? (if anything) or what are you trying to do?', 'adrotate'); ?><br />&raquo; <?php _e('Include error messages and/or relevant information.', 'adrotate'); ?><br />&raquo; <?php _e('Try to remember steps or actions you took that might have caused the problem.', 'adrotate'); ?></p>
							
								<p><label for="adrotate_updater_username"><strong><?php _e('Your name:', 'adrotate'); ?></strong><br /><input tabindex="1" name="adrotate_updater_username" type="text" class="search-input" style="width:100%;" value="<?php echo $firstname." ".$lastname;?>" autocomplete="off" /></label></p>
								<p><label for="adrotate_updater_email"><strong><?php _e('Your Email Address:', 'adrotate'); ?></strong><br /><input tabindex="1" name="adrotate_updater_email" type="text" class="search-input" style="width:100%;" value="<?php echo $user->user_email;?>" autocomplete="off" /></label></p>
								<p><label for="adrotate_updater_subject"><strong><?php _e('Subject:', 'adrotate'); ?></strong><br /><input tabindex="2" name="adrotate_updater_subject" type="text" class="search-input" style="width:100%;" value="" autocomplete="off" /></label></p>
								<p><label for="adrotate_updater_message"><strong><?php _e('Problem description / Question:', 'adrotate'); ?></strong><br /><textarea tabindex="3" name="adrotate_updater_message" style="width:100%; height:100px;"></textarea></label></p>
							
								<p><strong><?php _e('When you send this form the following data will be submitted:', 'adrotate'); ?></strong></p>
								<p><em><?php _e('Your name, Account email address, Your website url and some basic WordPress information will be included with the ticket.', 'adrotate'); ?><br /><?php _e('This information is treated as confidential and is mandatory.', 'adrotate'); ?></em></p>
							
								<p class="submit">
									<input tabindex="4" type="submit" name="adrotate_license_support_submit" class="button-primary" value="<?php _e('Post Ticket', 'adrotate'); ?>" />&nbsp;&nbsp;&nbsp;<em><?php _e('Please use english or dutch only!', 'adrotate'); ?></em>
								</p>
							
							</form>
				
						<?php 
						} else {
						?>
							<p><?php _e('Please register your copy of AdRotate Professional.', 'adrotate'); ?></p>
							<p class="submit">
								<?php if(adrotate_is_networked()) { ?>
									<a href="<?php echo network_admin_url('admin.php?page=adrotate'); ?>" class="button-primary"><?php _e('Register License', 'adrotate'); ?></a>
								<?php } else { ?>
									<a href="<?php echo admin_url('admin.php?page=adrotate-settings'); ?>" class="button-primary"><?php _e('Register License', 'adrotate'); ?></a>	
								<?php } ?>
								<em><?php _e('Contact your site administrator if you do not know what this means.', 'adrotate'); ?></em>
							</p>
					<?php 
						}
					} else {
						_e('Your server doesn\'t support JSON Encode - The support form can not be used. If you need help please post your ticket through adrotate-support@ajdg.net.', 'adrotate');
					}
					?>
					</div>
				</div>

			</div>
		</div>

		<div id="postbox-container-3" class="postbox-container" style="width:50%;">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
						
				<h3><?php _e('AdRotate News and Developer Blog', 'adrotate'); ?></h3>
				<div class="postbox-adrotate">
					<div class="inside">
						<?php wp_widget_rss_output(array(
							'url' => array('http://feeds.feedburner.com/meandmymacnet', 'http://ajdg.solutions/feed/',), 
							'title' => 'AdRotate Development News', 
							'items' => 4, 
							'show_summary' => 1, 
							'show_author' => 0, 
							'show_date' => 1)
							); ?>
					</div>
				</div>

				<h3><?php _e('AdRotate is brought to you by', 'adrotate'); ?></h3>
				<div class="postbox-adrotate">
					<div class="inside">
						<p><a href="https://ajdg.solutions/" title="AJdG Solutions"><img src="<?php echo WP_CONTENT_URL; ?>/plugins/adrotate-pro/images/ajdg-logo-100x60.png" alt="ajdg-logo-100x60" width="100" height="60" align="left" style="padding: 0 10px 10px 0;" /></a>
						<a href="https://ajdg.solutions/" title="AJdG Solutions">AJdG Solutions</a> - <?php _e('Your one stop for Webdevelopment, consultancy and anything WordPress! If you need a custom plugin. Theme customizations or have your site moved/migrated entirely. Visit my website for details!', 'adrotate'); ?> <a href="https://ajdg.solutions/" title="AJdG Solutions"><?php _e('Find out more', 'adrotate'); ?></a>!</p>

						<p><center><a href="https://twitter.com/AJdGSolutions" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @AJdGSolutions</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></center></p>
					</div>
				</div>

			</div>	
		</div>

	</div>

	<div class="clear"></div>
	<p><?php echo adrotate_trademark(); ?></p>
</div>