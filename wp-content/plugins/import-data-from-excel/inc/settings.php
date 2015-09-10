<?php
if ( !class_exists( 'Fitwp_Import_Data_From_Excel_Settings' ) )
{
	/**
	*
	*/
	class Fitwp_Import_Data_From_Excel_Settings
	{
		/**
		 * Class constructor
		 * Add hooks to ASQ
		 */
		function __construct()
		{
			// Adding control file upload to upload file excel
			add_action( 'admin_menu', array( $this, 'add_to_sub_menu' ), 21 );
			// Enqueue file style
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 *	Add sub menu Import Excel Data
		 *
		 *
		 * @return void
		 */
		function add_to_sub_menu()
		{
			add_submenu_page( 'tools.php', 'Import Excel Data','Import Excel Data', 'manage_options', 'import-excel-data', array( $this, 'fitwp_upload_file' ) );
		}

		/**
		 * Displaying file upload
		 *
		 *
		 * @return void
		 */
		function fitwp_upload_file()
		{
		?>
			<h2>Import Data</h2>
			<form method="post" enctype="multipart/form-data">
			*.XLSX <input type="file" name="file_data" class="button-upload" />&nbsp;&nbsp;<input class="button-import" type="submit" value="Import" />
			</form>
		<?php
		}

		/**
		 * Enqueue styles
		 *
		 * @return void
		 */
		function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'asq-import-excel-style', EXCEL_CSS_URL . 'style.css' );
		}

	}

	new Fitwp_Import_Data_From_Excel_Settings;
}