<?php
if ( !class_exists( 'WP_List_Table' ) )
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class SH_Log_Table extends WP_List_Table
{
	function __construct()
	{
		parent::__construct( array(
			'singular' => __( 'log', 'sch' ), //singular name of the listed records
			'plural'   => __( 'logs', 'sch' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		) );
	}

	function column_default( $item, $column_name )
	{
		switch ( $column_name )
		{
			case 'id':
			case 'time':
			case 'type':
			case 'action':
			case 'description':
				return $item[$column_name];
			case 'object':
				$post = get_post( $item[$column_name] );
				return $post->post_title;
			case 'user':
				$user = get_userdata( $item[$column_name] );
				$name = $user->display_name;
				if ( $user->first_name && $user->last_name )
					$name = "{$user->first_name} {$user->last_name}";
				return $name;
			default:
				return '';
		}
	}

	function get_columns()
	{
		$columns = array(
			'id'          => __( 'ID', 'sch' ),
			'time'        => __( 'Time', 'sch' ),
			'type'        => __( 'Type', 'sch' ),
			'action'      => __( 'Action', 'sch' ),
			'description' => __( 'Description', 'sch' ),
			'object'      => __( 'Company', 'sch' ),
			'user'        => __( 'User', 'sch' ),
		);
		return $columns;
	}

	function prepare_items()
	{
		global $wpdb;

		$per_page = 20;
		$current_page = $this->get_pagenum();

		$company_id = isset( $_GET['company_id'] ) ? intval( $_GET['company_id'] ) : -1;
		$where = '';
		if ( -1 != $company_id && $company_id > 0 )
			$where = " WHERE object = '$company_id'";
		$sql = "SELECT COUNT(*) FROM " . SH_TABLE . "$where ORDER BY time DESC;";

		// Get total items
		$total_items = $wpdb->get_var( $sql );

		// Get all needed items
		$offset = ( $current_page - 1 ) * $per_page;
		$sql = "SELECT * FROM " . SH_TABLE . "$where ORDER BY time DESC LIMIT $per_page OFFSET $offset;";
		$this->found_data = $wpdb->get_results( $sql, ARRAY_A );

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );
		$this->items = $this->found_data;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

} // SH_Log_Table Class

add_action( 'company_account_page_after', 'sch_display' );

/**
 * Show history data in account page
 *
 * @return void
 */
function sch_display()
{
	?>
	<h3><?php _e( 'My Account History', '7listings' ); ?></h3>

	<div class="data-grid" id="sl-history">
		<div class="header">
			<div class="date"><?php _e( 'Date', '7listings' ); ?></div>
			<div class="type"><?php _e( 'Type', '7listings' ); ?></div>
			<div class="action"><?php _e( 'Action', '7listings' ); ?></div>
			<div class="description"><?php _e( 'Description', '7listings' ); ?></div>
		</div>
		<?php
		global $wpdb;
		$sql = '
			SELECT * FROM ' . SH_TABLE . '
			WHERE user=' . get_current_user_id() . '
			ORDER BY time DESC;
		';
		$data = $wpdb->get_results( $sql );

		$tpl = '
			<div class="row">
				<div class="date">%s</div>
				<div class="type">%s</div>
				<div class="action">%s</div>
				<div class="description">%s</div>
			</div>
		';
		foreach ( $data as $row )
		{
			printf( $tpl, date( 'd/m/Y', strtotime( $row->time ) ), $row->type, $row->action, $row->description );
		}
		?>
	</div>
<?php
}

add_action( 'wp_enqueue_scripts', 'sch_enqueue' );

/**
 * Enqueue scripts and styles for plugin
 *
 * @return void
 */
function sch_enqueue()
{
	if ( is_page() && get_the_ID() == sl_setting( 'company_page_account' ) )
		wp_enqueue_style( 'sch', SH_URL . 'style.css' );
}

add_action( 'admin_menu', 'sch_report_menu' );

function sch_report_menu()
{
	add_menu_page( __( 'Report', 'sch' ), __( 'Report', 'sch' ), 'manage_options', '7company-history', 'sch_report_page' );
}

function sch_report_page()
{
	echo '<div class="wrap"><h2>' . __( 'Company History Report', 'sch' ) . '</h2>';
	$table = new SH_Log_Table();
	$table->prepare_items();
	?>
	<form method="get" action="">
		<input type="hidden" name="page" value="7company-history">

		<p class="search-box">
			<select name="company_id">
				<option value="-1"><?php _e( 'All', 'sch' ); ?></option>
				<?php
				$companies = get_posts( array(
					'post_type'      => 'company',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'asc',
				) );
				$value = isset( $_GET['company_id'] ) ? $_GET['company_id'] : -1;
				foreach ( $companies as $company )
				{
					echo '<option value="' . $company->ID . '"' . selected( $value, $company->ID ) . '>' . $company->post_title . '</option>';
				}
				?>
			</select>
			<?php submit_button( __( 'Go', 'sch' ), 'button', '', false ); ?>
		</p>
	</form>
	<?php
	$table->display();
	echo '</div>';
}
