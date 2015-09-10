<?php

/**
 * Register shortcodes and output them in the frontend
 */
class Sl_Shortcodes_Frontend
{
	/**
	 * Hold all custom js code
	 *
	 * @var array
	 */
	public $js = array();

	/**
	 * List of markers for map shortcode. Each marker is an array of (address, latitude, longitude, content, marker_title)
	 * @var array
	 */
	public $markers = array();

	/**
	 * Counter for tabs
	 *
	 * @var integer
	 */
	static $counter_tab = 0;
	static $tab_active = false;

	/**
	 * Constructor
	 *
	 * @return Sl_Shortcodes_Frontend
	 */
	function __construct()
	{
		// Enqueue shortcodes scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_footer', array( $this, 'footer' ), 1000 );

		add_filter( 'sls_content', 'do_shortcode' );
		add_filter( 'sls_content', array( __CLASS__, 'cleanup' ) );

		// Register shortcodes
		$shortcodes = array(
			'button',
			'styled_box',
			'framed_image',
			'map',
			'marker',
			'widget_area',
			'toggle',
			'tabs',
			'tab',
			'accordions',
			'accordion',
			'divider',
			'icon',
			'tooltip',
		);
		foreach ( $shortcodes as $shortcode )
		{
			add_shortcode( $shortcode, array( $this, $shortcode ) );
		}
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Display custom js code
	 *
	 * @return void
	 */
	function footer()
	{
		if ( empty( $this->js ) )
			return;

		// Load Google maps only when needed
		echo '<script>if ( typeof google !== "object" || typeof google.maps !== "object" )
			document.write(\'<script src="//maps.google.com/maps/api/js?sensor=false"><\/script>\')</script>';
		$code = implode( '', $this->js );
		$code = defined( 'WP_DEBUG' ) && WP_DEBUG ? $code : sl_js_minify( $code );
		echo '<script>jQuery(function($){' . $code . '} )</script>';
	}

	/**
	 * Remove empty <br>, <p> tags
	 *
	 * @param  string $text
	 *
	 * @return string
	 */
	static function cleanup( $text )
	{
		$text     = str_replace( array( '<br>', '<br />', '<p></p>' ), '', $text );
		$patterns = array(
			'#^\s*</p>#',
			'#<p>\s*$#',
		);

		return preg_replace( $patterns, '', $text );
	}

	/**
	 * Show button shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function button( $atts, $content )
	{
		extract( shortcode_atts( array(
			'link'          => '#',
			'id'            => '',
			'nofollow'      => '',
			'background'    => '',
			'text_color'    => '',
			'target'        => '',

			'icon'          => '',
			'icon_position' => '',

			'size'          => '',
			'color'         => '',
			'align'         => '',
			'full'          => '',
			'class'         => '',
		), $atts ) );

		$classes = array( 'button' );
		if ( $full )
			$classes[] = 'full';
		$params = array(
			'color',
			'align',
			'class',
			'size',
		);
		foreach ( $params as $class )
		{
			if ( $$class )
				$classes[] = $$class;
		}
		$classes = implode( ' ', $classes );
		$style   = '';
		if ( $background )
			$style .= "background:$background;";
		if ( $text_color )
			$style .= "color:$text_color;";

		$content = apply_filters( 'sls_content', $content );
		if ( $icon && $icon_position )
		{
			$icon    = "<i class='icon-$icon'></i>";
			$content = $icon_position == 'after' ? ( $content . $icon ) : ( $icon . $content );
		}

		return "<a href='$link' class='$classes'" .
		( $id ? "id=' $id'" : '' ) .
		( $nofollow ? " rel='nofollow'" : '' ) .
		( $target ? " target='$target'" : '' ) .
		( $style ? " style='$style" : '' ) .
		'>' . $content . '</a>';
	}

	/**
	 * Show styled boxes shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function styled_box( $atts, $content )
	{
		extract( shortcode_atts( array(
			'style'   => '',
			'color'   => '',
			'rounded' => '',
			'title'   => '',
			'text'    => '',
			'type'    => '',
		), $atts ) );

		$content = apply_filters( 'sls_content', $content );

		if ( $style == 'alert' )
			return '<div class="alert' . ( $type ? " alert-$type" : '' ) . '">' . ( $title ? "<h4>$title</h4>" : '' ) . $content . '</div>';
		else
			return '<div class="color-box' . ( $color ? " $color" : '' ) . ( $rounded ? ' rounded' : '' ) . '">' . $content . '</div>';
	}

	/**
	 * Show framed image shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function framed_image( $atts, $content )
	{
		extract( shortcode_atts( array(
			'type'   => '',
			'url'    => '',
			'title'  => '',
			'width'  => '',
			'height' => '',
			'align'  => '',
		), $atts ) );

		return "<img class='img-$type $align' src='$url' title='$title' alt='$title' width='$width' height='$height'>";
	}

	/**
	 * Show divider shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function divider( $atts, $content )
	{
		return '<hr>';
	}

	/**
	 * Show map shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function map( $atts, $content )
	{
		$default_controls = sl_setting( 'design_map_controls' );

		extract( shortcode_atts( array(
			'type'             => 'address',
			'address'          => '',
			'latitude'         => '',
			'longtitude'       => '',
			'map_type'         => sl_setting( 'design_map_type' ),
			'marker_title'     => '',
			'marker_animation' => sl_setting( 'design_map_marker_animation' ),
			'zoom'             => sl_setting( 'design_map_zoom' ),
			'width'            => '100%',
			'height'           => '400px',
			'align'            => 'none',
			'scrollwheel'      => in_array( 'scrollwheel', $default_controls ),
			'disable_dragging' => sl_setting( 'design_map_disable_dragging' ),
			'controls'         => implode( ',', $default_controls ),
			'marker_icon'      => 'custom' == sl_setting( 'design_map_marker_style' ) && sl_setting( 'design_map_marker' ) ? sl_setting( 'design_map_marker' ) : '',

			'output_js'        => false, // Output JS or return HTML + JS (for shortcode)
			'js_callback'      => '', // JS callback

			'id'               => '', // Custom ID and class
			'class'            => '',
		), $atts ) );

		// Reset markers array
		$this->markers = array();

		do_shortcode( $content );

		list( $html, $js ) = sl_map( $atts, $content, $this->markers );

		$this->js[] = $js;

		return $html;
	}

	/**
	 * Show marker shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function marker( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'address'      => '',
			'latitude'     => '',
			'longitude'    => '',
			'icon'         => '',
			'marker_title' => '',
			'animation'    => '',
		), $atts );

		$this->markers[] = array(
			'address'      => $atts['address'],
			'latitude'     => $atts['latitude'],
			'longitude'    => $atts['longitude'],
			'icon'         => $atts['icon'],
			'marker_title' => $atts['marker_title'],
			'animation'    => $atts['animation'],
			'content'      => $content,
		);

		return '';
	}

	/**
	 * Show widget_area shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function widget_area( $atts, $content )
	{
		extract( shortcode_atts( array(
			'id' => '',
		), $atts ) );
		if ( ! $id )
			return '';

		ob_start();
		dynamic_sidebar( $id );

		return ob_get_clean();
	}

	/**
	 * Show toggle shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function toggle( $atts, $content )
	{
		extract( shortcode_atts( array(
			'icon'  => 'caret',
			'title' => '',
		), $atts ) );
		if ( ! $title || ! $content )
			return '';

		$content = apply_filters( 'sls_content', $content );

		return sprintf( '
		<div class="toggle %s">
			<a href="#"><h4 class="title">%s</h4></a>
			<div class="content">%s</div>
		</div>',
			$icon,
			$title,
			$content
		);
	}

	/**
	 * Show tabs shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function tabs( $atts, $content )
	{
		// Get all tab titles
		preg_match_all( '#\[tab [^\]]*?title=[\'"]?(.*?)[\'"]#', $content, $matches );

		if ( empty( $matches[1] ) )
			return '';

		self::$counter_tab ++;
		$tpl = '<li%s><a href="#tab%s-%s" data-toggle="tab">%s</a></li>';
		$lis = '';
		foreach ( $matches[1] as $k => $title )
		{
			$lis .= sprintf( $tpl, $k ? '' : ' class="active"', self::$counter_tab, sanitize_key( $title ), $title );
		}
		self::$tab_active = true;
		$content          = apply_filters( 'sls_content', $content );

		return sprintf(
			'<div class="tabs-container">
				<ul class="nav nav-tabs">%s</ul>
				<div class="tab-content">%s</div>
			</div>',
			$lis,
			$content
		);
	}

	/**
	 * Show tab shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function tab( $atts, $content )
	{
		extract( shortcode_atts( array(
			'title' => '',
		), $atts ) );
		$class            = self::$tab_active ? ' in active' : '';
		self::$tab_active = false;

		$content = apply_filters( 'sls_content', $content );

		return sprintf(
			'<div class="tab-pane fade%s" id="tab%s-%s">%s</div>',
			$class,
			self::$counter_tab,
			sanitize_key( $title ),
			$content
		);
	}


	/**
	 * Show accordions shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function accordions( $atts, $content )
	{
		extract( shortcode_atts( array(
			'icon' => 'caret',
		), $atts ) );
		$content = apply_filters( 'sls_content', $content );

		return sprintf(
			'<div class="accordions %s">%s</div>',
			$icon,
			$content
		);
	}

	/**
	 * Show accordion shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function accordion( $atts, $content )
	{
		extract( shortcode_atts( array(
			'title' => '',
		), $atts ) );
		if ( ! $title || ! $content )
			return '';

		$content = apply_filters( 'sls_content', $content );

		return sprintf( '
			<div class="accordion">
				<a href="#"><h4 class="title">%s</h4></a>
				<div class="content">%s</div>
			</div>',
			$title,
			$content
		);
	}

	/**
	 * Show tooltip shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function tooltip( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'content'   => '',
			'type'      => '',
			'placement' => '',
		), $atts );
		if ( ! $atts['content'] || ! $content )
			return '';

		$content = apply_filters( 'sls_content', $content );

		return sprintf(
			'<a href="#" data-toggle="tooltip" data-html="true" class="sl-tooltip %s" data-placement="%s" title="%s">%s</a>',
			$atts['type'],
			$atts['placement'] ? $atts['placement'] : 'top',
			esc_attr( $atts['content'] ),
			$content
		);
	}

	/**
	 * Show icon shortcodes
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function icon( $atts, $content )
	{
		extract( shortcode_atts( array(
			'icon'        => '',
			'size'        => '',
			'rotate'      => '',
			'animation'   => '',
			'effect'      => '',
			'border'      => '',
			'align'       => '',
			'color'       => '',
			'stack'       => '',
			'stack_icon'  => '',
			'stack_size'  => '',
			'stack_color' => '',
		), $atts ) );

		$classes = array();
		$params  = array(
			'icon',
			'size',
			'rotate',
			'effect',
		);
		foreach ( $params as $param )
		{
			$$param && $classes[] = 'icon-' . $$param;
		}
		$align && $classes[] = "pull-$align";
		$animation && $classes[] = 'icon-spin';
		$border && $classes[] = 'icon-border';
		$color && $classes[] = $color;

		$icon = sprintf( '<i class="%s"></i>', implode( ' ', $classes ) );
		$html = $icon;

		// For stack
		if ( $stack )
		{
			$classes = array( 'icon-stack-base' );
			$stack_icon && $classes[] = "icon-$stack_icon";
			$stack_size && $classes[] = "icon-$stack_size";
			$stack_color && $classes[] = $stack_color;

			$html = '<span class="icon-stack' . ( $stack_size ? " is-$stack_size" : '' ) . '">' . $icon . sprintf( '<i class="%s"></i>', implode( ' ', $classes ) ) . '</span>';
		}

		return $html;
	}
}
