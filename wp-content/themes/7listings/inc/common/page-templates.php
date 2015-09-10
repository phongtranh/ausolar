<?php
add_filter( 'theme_page_templates', 'sl_module_page_templates' );

/**
 * Remove page templates for modules if they're not active
 *
 * @param array $page_templates List of page templates array( file => name )
 *
 * @return array
 */
function sl_module_page_templates( $page_templates )
{
	if ( ! Sl_License::is_module_activated( 'company' ) || ! Sl_License::is_module_enabled( 'company', false ) )
		unset( $page_templates['templates/company-admin.php'] );

	return $page_templates;
}
