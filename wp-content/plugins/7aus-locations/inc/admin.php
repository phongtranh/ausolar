<?php
namespace Sl\Locations\Aus;

/**
 * This class adds settings page for the plugin under "Settings" menu
 * It also enqueues scripts and handles upload, processes data
 *
 * @package    Sl
 * @subpackage Sl\Locations\Aus
 */
class Admin
{
	/**
	 * Store page ID, used to show admin notices
	 * @var string
	 */
	public static $page_hook;

	/**
	 * Add hooks when class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
	}

	/**
	 * Add plugin menu under "Settings" menu
	 *
	 * @return void
	 */
	public static function add_menu()
	{
		// Add admin menu
		self::$page_hook = add_options_page( __( 'AUS Locations', '7aus-locations' ), __( 'AUS Locations', '7aus-locations' ), 'manage_options', '7aus-locations', array( __CLASS__, 'show_page' ) );

		// Show admin notices
		add_action( 'load-' . self::$page_hook, array( __CLASS__, 'admin_notices' ) );

		// Handle upload
		add_action( 'load-' . self::$page_hook, array( __CLASS__, 'handle_upload' ) );

		// Enqueue script
		add_action( 'admin_print_styles-' . self::$page_hook, array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * Enqueue script for settings page
	 *
	 * @return void
	 */
	public static function enqueue()
	{
		wp_enqueue_script( '7aus-locations', plugins_url( 'js/locations.js', __DIR__ ) );
	}

	/**
	 * Show settings page
	 *
	 * @return void
	 */
	public static function show_page()
	{
		?>
		<div class="wrap">
			<h2><?php _e( 'Australia Locations Import', '7aus-locations' ); ?></h2>
			<?php
			$file_path = self::get_file_path();
			if ( file_exists( $file_path ) )
			{
				?>
				<h3><?php _e( 'Uploaded file', '7aus-locations' ); ?></h3>
				<pre><code><?php echo $file_path; ?></code></pre>
				<p class="submit">
					<a href="#" class="button button-primary" id="process" data-nonce="<?php echo wp_create_nonce( 'process' ); ?>" data-done_text="<?php esc_attr_e( 'Done', '7aus-locations' ); ?>"><?php _e( 'Process data', '7aus-locations' ); ?></a>
					<span class="spinner"></span>
				</p>
				<div id="status"></div>
			<?php
			}
			?>

			<?php
			if ( file_exists( $file_path ) )
				echo '<h3>' . __( 'Upload new file', '7aus-locations' ) . '</h3>';
			?>
			<form method="post" action="" enctype="multipart/form-data">
				<?php wp_nonce_field( 'upload' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="blogname"><?php _e( 'Select <code>.xlsx</code> file', '7aus-locations' ); ?></label>
						</th>
						<td>
							<input type="file" id="file" name="locations">
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Upload', '7aus-locations' ) ); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Show admin notices
	 *
	 * @return void
	 */
	public static function admin_notices()
	{
		settings_errors( '7aus-locations' );
	}

	/**
	 * Save uploaded file in upload folder
	 *
	 * @return void
	 */
	public static function handle_upload()
	{
		// Check nonce and upload file
		if (
			! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'upload' )
			|| empty( $_FILES['locations'] )
		)
		{
			return;
		}

		$file = $_FILES['locations'];

		// Check for upload error
		if ( $file['error'] != UPLOAD_ERR_OK )
		{
			add_settings_error( '7aus-locations', 'error', __( 'Error uploading file. Please try again.' ) );

			return;
		}

		// Check for file extension
		$extension = substr( $file['name'], strrpos( $file['name'], '.' ) );
		if ( '.xlsx' != $extension )
		{
			add_settings_error( '7aus-locations', 'error', __( 'Invalid file type. Please try again.' ) );

			return;
		}

		// Save file to upload folder
		$file_path = self::get_file_path();
		move_uploaded_file( $file['tmp_name'], $file_path );

		add_settings_error( '7aus-locations', 'error', __( 'File uploaded successfully.' ), 'updated' );
	}

	/**
	 * Get path of uploaded file
	 *
	 * @return string
	 */
	public static function get_file_path()
	{
		$file_name  = 'aus-locations.xlsx';
		$upload_dir = wp_upload_dir();

		return path_join( $upload_dir['basedir'], $file_name );
	}
}
