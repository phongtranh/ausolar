<?php
/** 
 * The Google Content Experiments Class
 */
class GCE {

	public function __construct() {
		add_action( 'wp', array( $this, 'setup' ) );
	}
	
	/** 
	 * Function executed at start
	 */
	public function setup() {
		// Don't output Content Experiment code when showing multiple posts
		if ( !is_singular() )
			return;
		
		// Else, continue
		global $post;
		$wpe_gce_active = get_post_meta( $post->ID, '_wpe_gce_active', TRUE );
		
		// Add code if Content Experiment is enabled for this post/page
		if( isset( $wpe_gce_active ) && $wpe_gce_active == '1' ) {
			add_action( 'wp', array( $this, 'prepare_output' ), 0 );
		}
	}

	/** 
	 * Checks how to insert the code
	 */
	public function prepare_output() {
		// Add output_code function to custom hook
		add_action( 'wpe_gce_head', array( $this, 'output_code' ), 0 );
		
		// Check if active theme is supported
		switch ( $this->check_theme() ) {
			case 'genesis':
				remove_action( 'genesis_doctype', 'genesis_do_doctype' );			
				if ( current_theme_supports( 'genesis-html5' ) ) {
					$this->support_genesis(true);
				} else {
					$this->support_genesis();
				}
				break;
			case 'infinity':
				add_action( 'open_head', array( $this, 'output_code' ), 0 );
				break;
			case 'thematic':
				add_filter( 'thematic_head_profile', array( $this, 'support_thematic' ), 0 );
				break;
			case 'pagelines':
				add_action( 'pagelines_head', array( $this, 'output_code' ), 0 );
				break;
			default:
				// Theme not supported, insert hook manually
				break;
		}
	}
	
	/** 
	 *  The code to output after the <head> tag
	 */
	public function output_code() {
		global $post;
		$wpe_gce_code = get_post_meta( $post->ID, '_wpe_gce_code', TRUE );
		echo $wpe_gce_code . PHP_EOL;;
	}
	
	/**
	 * Check which theme is active
	 *
	 * @return string
	 */
	public function check_theme() {
		$active_theme = wp_get_theme();
		return $active_theme->{'Template'};
	}
	
	/**
	 * Insert code into Genesis
	 *
	 * @since 1.0.2
	 * @param bool	$html5	Whether the child theme supports HTML5 or not.
	 */	
	public function support_genesis( $html5 = false ) {
		if( $html5 == true ) { ?>
<!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?>>
<head>
<?php $this->output_code(); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
		} else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?>>
<head profile="http://gmpg.org/xfn/11">
<?php $this->output_code(); ?>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<?php
		}
	}	
	
	/**
	 * Insert code into Thematic
	 *
	 * @since 1.0.2
	 * @param string $content	Head information from Thematic.
	 */	
	public function support_thematic( $content ) {
		echo $content;
		$this->output_code();
	}

}
	
?>