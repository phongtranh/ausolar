<?php
/** 
 * The Google Content Experiments Admin Class
 */
class GCEAdmin {
	const LANG = 'gce';
	private $data;
	
	public function __construct() {
		add_action ( 'add_meta_boxes', array( $this, 'gwe_meta_box' ) );
		add_action( 'save_post', array( $this, 'gwe_save_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'gwe_admin_scripts' ) );
	}
	
	public function gwe_meta_box() {
		// Default post types
		$post_types = array( 'post', 'page' );
		
		// Add custom post types to the array
		$custom_types = get_post_types( array( 'public' => true, '_builtin' => false ) );
		foreach( $custom_types as $custom_post_type ) {
			$post_types[] = $custom_post_type;	
		}
		
		// Add meta box to each post type
		foreach( $post_types as $post_type ) {
			add_meta_box( 
				 'gce-meta',
				__( 'Google Content Experiments settings', self::LANG ),
				array( $this, 'render_meta_box_content' ),
				$post_type,
				'advanced',
				'high'
			);
		}
	}
	
	public function render_meta_box_content() {
		global $post;

		$pt_object = get_post_type_object( $post->post_type );
		$wpe_gce_active = get_post_meta( $post->ID, '_wpe_gce_active', true );
		$wpe_gce_code = get_post_meta( $post->ID, '_wpe_gce_code', true );
		
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'wpe_gce' );
				
		$output = '<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">';
		
		// Checkbox to activate GCE on this page
		$output .= '<label for="wpe_gce_active">';
		$output .= sprintf( __( '%1$s is the %2$s of a Content Experiment', self::LANG), $pt_object->labels->singular_name, '<strong>'._x( 'original page', 'Name that Google uses for the main page of the experiment', self::LANG ).'</strong>' );
		$output .= '&nbsp;&nbsp;</label>';
		$output .= '<input id="wpe_gce_active" name="_wpe_gce_active" type="checkbox" value="1" '.checked( $wpe_gce_active, 1, false ).'>';
		
		// Google Content Experiment code
		$display = $wpe_gce_active == 1 ? '' : ' display:none;';
		$output .= '<div class="contexpcode" style="margin-top:10px;'.$display.'">';
		$output .= '<label for="wpe_gce_code">';
		$output .= __( 'Your Content Experiment code', self::LANG );
		$output .= ':</label><br />';
		$output .= '<textarea id="wpe_gce_code" name="_wpe_gce_code" cols="70" rows="10">'.$wpe_gce_code.'</textarea>';
		$output .= '</div>';
		$output .= '</form>';
		echo $output;
	}
	
	public function gwe_save_meta( $post_id ) {
		
		// Don't save data on auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! isset( $_POST['wpe_gce'] ) || ! wp_verify_nonce( $_POST['wpe_gce'], plugin_basename( __FILE__ ) ) )
			return;


		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;
		}

		// OK, we're authenticated: we need to find and save the data
		$this->data = array(
						'_wpe_gce_active' => $_POST['_wpe_gce_active'],
						'_wpe_gce_code' => $_POST['_wpe_gce_code']
					);
		// Loop through array to save the data
		foreach( $this->data as $meta_key => $meta_value ) {
		  update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
	
	public function gwe_admin_scripts() {
	   wp_register_script( 'gwe-meta-box', plugins_url( 'js/admin.js' , dirname(__FILE__) ), array('jquery'), null, true );
	   wp_enqueue_script( 'gwe-meta-box' );
	}
	
}
?>