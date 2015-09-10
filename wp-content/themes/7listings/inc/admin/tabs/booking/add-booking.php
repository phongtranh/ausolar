<?php
if( empty( $_GET['action'] ) )
{
?>
<div class="media-modal" id="booking-details">
	<a class="media-modal-close" href="<?php echo admin_url() . 'edit.php?post_type=booking'; ?>"><span class="media-modal-icon"></span></a>
	<div class="media-modal-content">
		<div class="media-frame hide-menu hide-router">
			<div class="media-frame-title">
				<h1><?php _e( 'Booking Details', '7listings' ); ?></h1>
			</div>
			<div class="media-frame-content" id="booking-form">
				<div class="settings">
					<section id="resource-select" class="section step1">
						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Type', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<div class="post-type">
									<div class="sl-input post-type-input">
										<input type="radio" class="sl-post-type" id="accommodations" name="sl-post-type" value="<?php _e( 'accommodation', '7listings' ); ?>" tabindex="210">
										<label for="accommodations" class="accommodations icon" title="<?php _e( 'Select Accommodations', '7listings' ); ?>">
											<?php _e( 'Accommodations', '7listings' ); ?>
										</label>
										<input type="radio" class="sl-post-type" id="tours" name="sl-post-type" value="<?php _e( 'tour', '7listings' ); ?>" tabindex="210">
										<label for="tours" class="tours icon" title="<?php _e( 'Select Tours', '7listings' ); ?>">
											<?php _e( 'Tours', '7listings' ); ?>
										</label>
										<input type="radio" class="sl-post-type" id="rentals" name="sl-post-type" value="<?php _e( 'rental', '7listings' ); ?>" tabindex="210">
										<label for="rentals" class="rentals icon" title="<?php _e( 'Select Rentals', '7listings' ); ?>">
											<?php _e( 'Rentals', '7listings' ); ?>
										</label>
									</div>
								</div>
								<span class="spinner"></span>
							</div>
						</div>
						
						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Listing', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<select class="sl-post" name="sl-post"></select>
								<span class="spinner"></span>
							</div>
						</div>
	
						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Resource', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<select class="sl-resource" name="sl-resource"></select>
								<span class="spinner"></span>
							</div>
						</div>
					</section>
					
					<section id="booking-info" class="section step2 hidden">
						<h2><?php _e( 'Booking Info', '7listings' ); ?></h2>
						<div class="rental-resources">
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'From', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="rental-checkin" id="rental-checkin" class="datepicker sl-input-medium" readonly>
								</div>
							</div>

							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'To', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="rental-checkout" id="rental-checkout" class="datepicker sl-input-medium" readonly>
								</div>
							</div>
							<div id="rental-upsells" class="hidden"></div>
						</div> <!-- Rental form -->

						<div class="accommodation-resources">
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'From', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="accommodation-checkin" id="accommodation-checkin" class="datepicker sl-input-medium" readonly>
								</div>
							</div>

							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'To', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="accommodation-checkout" id="accommodation-checkout" class="datepicker sl-input-medium" readonly>
								</div>
							</div>

							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Guests', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<select name="accommodation-guests" id="accommodation-guests" class="sl-input-tiny"></select>
									<?php _e( 'Occupancy', '7listings' ) ;?> :
									<span class="occupancy"></span> -
									<?php _e( 'Max Occupancy', '7listings' ) ;?> :
									<span class="max-occupancy"></span>
								</div>
							</div>
						</div> <!-- Accommodation form -->

						<div class="tour-resources">
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Depart', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="tour-depart" id="tour-depart" class="datepicker sl-input-medium" readonly>
								</div>
							</div>

							<div class="sl-settings tour-daily-time hidden">
								<div class="sl-label">
									<label><?php _e( 'Time', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<select name="tour-time" id="tour-time" class="time-select sl-input-small"></select>
								</div>
							</div>

							<div id="tour-upsells" class="hidden"></div>
						</div> <!-- Tour form -->
					</section>
					
					<section id="guest-details" class="section step3 hidden">
						<h2><?php _e( 'Guest Details', '7listings' ); ?></h2>
						<div class="guest-details">
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Name', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="customer-first-name" id="customer-first-name" class="sl-input-medium" placeholder="<?php _e( 'First', '7listings' ); ?>">
									<input type="text" name="customer-last-name" id="customer-last-name" class="sl-input-medium" placeholder="<?php _e( 'Last', '7listings' ); ?>">
								</div>
							</div>

							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Email', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="customer-email" id="customer-email" class="sl-input-large">
								</div>
							</div>

							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Phone', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="text" name="customer-phone" id="customer-phone" class="sl-input-large">
								</div>
							</div>

							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Message', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<textarea name="customer-message" cols="42" rows="5" id="customer-message"></textarea>
								</div>
							</div>
						</div>

						<div>
							<input type="hidden" name="total-prices" id="total-prices">
						</div>
					</section>
					
				</div>
			</div>
			<div class="media-frame-toolbar">
				<div class="media-toolbar">
					<div class="total price">
						<strong><?php _e( 'Total', '7listings' ); ?></strong>
						<span class="total-prices"></span>
					</div>
					<div class="media-toolbar-primary">
						<a href="#" class="button media-button button-primary button-large media-button-select hidden" id="update"><?php _e( 'Update', '7listings' ); ?></a>
						<span class="spinner"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
}