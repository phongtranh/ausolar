<?php
add_action( 'after_setup_theme', array( Sl_Cart::get_instance(), 'on_load' ), 20 );

class Sl_Cart
{
	/**
	 * @var string Module name
	 */
	public $name = 'cart';

	/**
	 * @var object Class instance
	 */
	protected static $instance = null;

	/**
	 * @var array Contains an array of cart items
	 */
	public $content;

	const SESSION_KEY = 'sl_cart';

	/**
	 * Access this plugin's working instance
	 *
	 * @return Sl_Cart
	 */
	static function get_instance()
	{
		null === self::$instance && self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Constructor. Intentionally left empty and public.
	 */
	function __construct()
	{

	}

	/**
	 * Access the working instance
	 *
	 * @return void
	 */
	function on_load()
	{
		if ( is_admin() )
			require THEME_MODULES . $this->name . '/settings.php';

		if ( ! Sl_License::is_module_enabled( $this->name, false ) )
			return;

		if ( ! session_id() )
			session_start();

		$this->load_files();

		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );

		add_action( 'init', array( $this, 'init' ), 5 ); // Get cart on init
	}

	/**
	 * Load files
	 *
	 * @return void
	 */
	function load_files()
	{
		$dir = THEME_MODULES . $this->name;
		require "$dir/helper.php";

		if ( is_admin() )
		{
			require "$dir/booking-management.php";
			new Sl_Cart_Booking_Management;

			require "$dir/booking-edit.php";
			new Sl_Cart_Booking_Edit;

			if ( defined( 'DOING_AJAX' ) )
				require "$dir/ajax.php";
		}
		else
		{
			require "$dir/frontend.php";
		}
	}

	/**
	 * Create rewrite rule for cart pages
	 *
	 * @return void
	 */
	function add_rewrite_rules()
	{
		add_rewrite_rule( 'cart/?$', 'index.php?cart=1', 'top' );
	}

	/**
	 * Add booking query vars
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	function add_query_vars( $vars )
	{
		$vars[] = 'cart';

		return $vars;
	}

	/**
	 * Front page template
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	function template_include( $template )
	{
		global $wp_query;

		if ( get_query_var( 'cart' ) )
		{
			$wp_query->is_home       = false;
			$wp_query->is_front_page = false;

			return locate_template( 'templates/cart/cart.php' );
		}

		return $template;
	}

	/**
	 * Get cart content
	 *
	 * @return void
	 */
	function init()
	{
		$this->get_cart_from_session();
	}

	/**
	 * Check if product is in the cart and return cart item key.
	 *
	 * Cart item key will be unique based on the item and its properties, such as post and resource.
	 *
	 * @param int $post_id
	 * @param int $resource
	 *
	 * @return int Item key. -1 if not found
	 */
	function find_product_in_cart( $post_id = 0, $resource = 0 )
	{
		foreach ( $this->content as $key => $item )
		{
			if ( $post_id == $item['post'] && $resource == $item['resource'] )
				return $key;
		}

		return - 1;
	}

	/**
	 * Sets the php session data for the cart and coupons and re-calculates totals.
	 *
	 * @return void
	 */
	function set_session()
	{
		$_SESSION[self::SESSION_KEY] = $this->content;
	}

	/**
	 * Get the cart data from the PHP session and store it in class variables.
	 *
	 * @return void
	 */
	function get_cart_from_session()
	{
		$this->content = isset( $_SESSION[self::SESSION_KEY] ) && is_array( $_SESSION[self::SESSION_KEY] ) ? $_SESSION[self::SESSION_KEY] : array();
	}

	/**
	 * Get number of items in the cart.
	 *
	 * @return int
	 */
	function get_cart_contents_count()
	{
		return count( $this->content );
	}

	/**
	 * Get the cart data
	 *
	 * @return array
	 */
	function get_cart()
	{
		return $this->content;
	}

	/**
	 * Empty cart
	 *
	 * @return void
	 */
	function remove_all()
	{
		$this->content = array();
		$this->set_session();
	}
}
