<?php

class Sls_Helper
{
	/**
	 * Show modal header
	 *
	 * @param string $shortcode
	 * @param string $title      Modal title
	 * @param bool   $controller User controller?
	 *
	 * @return void
	 */
	static function modal_header( $shortcode, $title, $controller = false )
	{
		if ( $controller === true )
			$controller = str_replace( ' ', '', ucwords( str_replace( '_', ' ', $shortcode ) ) );
		printf( '
			<div class="modal fade sls-popup" id="sls-popup-%s"%s>
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h3 class="modal-title">%s</h3>
						</div>
						<div class="modal-body">',
			$shortcode,
			$controller ? ' ng-controller="Sls' . $controller . '"' : '',
			$title
		);
	}

	/**
	 * Show modal footer
	 *
	 * @param string $shortcode
	 * @param bool   $custom_action Change behavior of insert button? Will add new class for button
	 *
	 * @param string $format
	 *
	 * @return void
	 */
	static function modal_footer( $shortcode, $custom_action = false, $format = '' )
	{
		printf( '
						</div><!-- .modal-body -->
						<div class="modal-footer">
							<button type="button" class="button button-large" data-dismiss="modal">%s</button>
							<button type="button" class="button button-primary button-large sls-insert%s" data-type="{{sls_%s.output}}"%s>%s</button>
						</div>
					</div>
				</div>
			</div>',
			__( 'Cancel', '7listings' ),
			$custom_action ? '-' . str_replace( '_', '-', $shortcode ) : '',
			$shortcode,
			$format ? " data-format=\"$format\"" : '',
			__( 'Insert', '7listings' )
		);
	}

	/**
	 * Show angularjs checkbox
	 *
	 * @param string $name Angular model name
	 * @param string $id   HTML ID
	 * @param bool   $checked
	 *
	 * @return void
	 */
	static function checkbox_angular( $name, $id, $checked = false )
	{
		printf(
			'<span class="checkbox">
				<input type="checkbox" ng-model="%s" ng-true-value="1" ng-false-value="" id="%s" ng-init="%s = \'%s\'">
				<label for="%s">&nbsp;</label>
			</span>',
			$name,
			$id,
			$name,
			$checked ? 1 : '',
			$id
		);
	}

	/**
	 * Display shortcode attributes
	 *
	 * @param string $shortcode
	 * @param array  $atts
	 *
	 * @return string
	 */
	static function shortcode_atts( $shortcode, $atts )
	{
		$prefix = $shortcode ? "sls_$shortcode." : '';
		$text   = '';
		foreach ( (array) $atts as $att )
		{
			$text .= sprintf( '{{%1$s%2$s && (\' %2$s="\' + %1$s%2$s + \'"\') || \'\'}}', $prefix, $att );
		}

		return $text;
	}

	/**
	 * Display shortcode attribute when another attribute is presented
	 *
	 * @param string $shortcode Shortcode name
	 * @param string $att       Attribute needs to insert
	 * @param string $depend    Attribute which is depended on
	 *
	 * @return string
	 */
	static function shortcode_att_depend( $shortcode, $att, $depend )
	{
		$prefix = $shortcode ? "sls_$shortcode." : '';
		$text   = sprintf( '{{%1$s%3$s && %1$s%2$s && (\' %2$s="\' + %1$s%2$s + \'"\') || \'\'}}', $prefix, $att, $depend );

		return $text;
	}

	/**
	 * Display color schemes
	 *
	 * @param string $model_name
	 *
	 * @return string
	 */
	static function color_schemes( $model_name )
	{
		echo '<div class="sls-color-schemes">';

		$colors = array(
			'rosy',
			'pink',
			'pink-dark',
			'red',
			'magenta',
			'orange',
			'orange-dark',
			'yellow',
			'green-light',
			'green-lime',
			'green',
			'blue',
			'blue-dark',
			'indigo',
			'violet',
			'cappuccino',
			'brown',
			'brown-dark',
			'gray',
			'gray-dark',
			'black',
			'white',
		);
		foreach ( $colors as $color )
		{
			printf(
				'<label class="sls-color-scheme sls-background-%1$s">
					<input ng-model="%2$s" type="radio" name="%2$s" value="%1$s">
				</label>',
				$color,
				$model_name
			);
		}
		echo '</div>';
	}
}
