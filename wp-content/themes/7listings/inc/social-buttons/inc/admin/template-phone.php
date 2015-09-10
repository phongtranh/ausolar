<?php
/**
 * This file contains template for single contact point configuration
 */

/**
 * @var array $contact_point Array of bundled contact point configuration, has following format:
 * array(
 *        'phone'    => ...
 *        'type'     => ...
 *        'option'   => ...
 *        'area'     => ...
 *        'language' => ...
 * ),
 * @var int   $index         Contact point index
 *
 * These variables are passed via 'include'
 * @see knowledge-settings.php
 */

$name_format = THEME_SETTINGS . "[knowledge_graph_contact_points][$index][%s]";
$option_format = THEME_SETTINGS . "[knowledge_graph_contact_points][$index]";
?>
<div class="contact-point">
	<div class="sl-settings phone-number">
		<div class="sl-label">
			<label><?php _e( 'Phone', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( '<b>Required</b><br>An internationalized version of the phone number, starting with the “+” symbol and country code (+1 in the US and Canada).', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php printf( $name_format, 'phone' ); ?>" value="<?php echo esc_attr( $contact_point['phone'] ); ?>">
			<select name="<?php printf( $name_format, 'type' ); ?>">
				<?php
				Sl_Form::options( $contact_point['type'], array(
					'customer support'		=> 'customer support',
					'technical support' 	=> 'technical support',
					'billing support'		=> 'billing support',
					'bill payment'			=> 'bill payment',
					'sales'					=> 'sales',
					'reservations'			=> 'reservations',
					'credit card support'	=> 'credit card support',
					'emergency'				=> 'emergency',
					'baggage tracking'		=> 'baggage tracking',
					'roadside assistance'	=> 'roadside assistance',
					'package tracking'		=> 'package tracking'
				) );
				?>
			</select>
		</div>
	</div>
	<div class="sl-sub-settings">
		<div class="sl-settings number-type">
			<div class="sl-label">
				<label><?php _e( 'Option', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Optional', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php 
				$options = array(
					'TollFree'        			=> __( 'Toll Free', '7listings' ),
					'HearingImpairedSupported' 	=> __( 'Hearing Impaired Supported', '7listings' ),
				);
				
				if( ! isset( $contact_point['option'] ) || ! is_array( $contact_point['option'] ) )
					$value = array ( 'TollFree' );
				else 
					$value = $contact_point['option'];
				
				echo '<div class="sl-sub-settings contact-options">';
				foreach ( $options as $option => $label )
				{
					$id = uniqid();
					printf(
						'<div class="sl-settings">
							<div class="sl-input">
								<span class="checkbox">
									<input type="checkbox" id="%s" name="%s[option][]" value="%s"%s>
									<label for="%s">&nbsp;</label>
								</span>
							</div>
							<div class="sl-label append">
								<label>%s</label>
							</div>
						</div>',
						$id,
						$option_format,
						$option,
						checked( in_array( $option, $value ), true, false ),
						$id,
						$label
					);
				}
				echo '</div>';
				?>
			</div>
		</div>
		<div class="sl-settings area">
			<div class="sl-label">
				<label><?php _e( 'Area', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Optional<br>The geographical region served by the number. If omitted, the number is assumed to be global.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select class="knowledge-graph-areas" name="<?php printf( $name_format, 'area' ); ?>[]" multiple>
					<?php
					foreach	( $countries as $code => $name )
					{
						printf(
							'<option value="%s"%s>%s</option>',
							$code,
							selected( in_array( $code, $contact_point['area'] ), true, false ),
							$name
						);
					}
					?>
				</select>
			</div>
		</div>
		<div class="sl-settings language">
			<div class="sl-label">
				<label><?php _e( 'Language', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Optional<br>Languages may be specified by their common English name. If omitted, the language defaults to English.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php printf( $name_format, 'language' ); ?>" value="<?php echo esc_attr( $contact_point['language'] ); ?>" placeholder="<?php _e( 'ex: English, French', '7listings' ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label>&nbsp;</label>
			</div>
			<div class="sl-input">
				<a href="#" class="button delete-contact-point" title="<?php _e( 'Delete phone number', '7listings' ); ?>"><?php _e( 'Delete', '7listings' ); ?></a>
			</div>
		</div>
	</div>
</div>