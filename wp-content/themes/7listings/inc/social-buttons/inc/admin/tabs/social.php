<?php
$tpl     = '
	<div class="sl-settings sl-social-main-settings">
		<div class="sl-label">
			<label for="%1$s">%2$s</label>
		</div>
		<div class="sl-input">
			<span class="input-prepend">
				<span class="add-on"><i class="icon-%4$s"></i></span>
				<input type="text" id="%1$s" name="' . THEME_SETTINGS . '[%1$s]" value="%3$s">
			</span>
		</div>
	</div>';
$socials = array(
	'facebook'   => 'Facebook',
	'twitter'    => 'Twitter',
	'googleplus' => 'Google+',
	'pinterest'  => 'Pinterest',
	'linkedin'   => 'LinkedIn',
	'instagram'  => 'Instagram',
	'rss'        => 'RSS',
);
foreach ( $socials as $k => $v )
{
	$icon = $k == 'googleplus' ? 'google-plus' : $k;
	printf( $tpl, $k, $v, sl_setting( $k ), $icon );
}
