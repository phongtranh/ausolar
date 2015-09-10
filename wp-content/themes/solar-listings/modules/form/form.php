<?php

class Form
{
	/**
	 * Smarter way to generate <select> element 
	 * @param  string $name     name attribute
	 * @param  array  $options  options
	 * @param  string $selected selected element
	 * @param  array  $attrs    attributes
	 * @return void print output html
	 */
	public static function select( $name, $options = [], $selected = '', $attrs = [] )
	{
		if ( ! is_array( $options ) )
			return;

		$str_attrs = "";

		foreach ( $attrs as $k => $v )
			$str_attrs .= " {$k}=\"{$v}\"";

		$output = "<select name=\"{$name}\" {$str_attrs}>";

		if ( is_array( $selected ) && ! empty( $selected ) )
			$selected = array_swap( $selected );

		foreach ( $options as $value => $label )
		{
			if ( isset( $attrs['multiple'] ) && is_array( $selected ) )
			{
				$str_selected = ( isset(  $selected[$value] ) ) ? 'selected' : '';
			}
			else
			{
				$str_selected = ( $selected == $value || $selected == $label ) ? 'selected' : '';
			}

			$output .= "<option value=\"{$value}\" {$str_selected}>{$label}</option>";
		}

		echo $output . "</select>";
	}

	public static function checkbox( $name, $value, $checked = false )
	{
		$str_checked = $checked ? 'checked' : '';

		echo "<input type=\"checkbox\" name=\"$name\" value=\"$value\" $str_checked />";
	}

	/**
	* Generate a list of checkbox
	*
	* @param String $name Name of checkbox
	* @param Array $options Checkbox options
	* @param Array $checked Checked items
	* @param $attrs
	*/
	public static function checkboxList( $name, $options = [], $checked = [], $attrs = [])
	{
		if ( ! is_array( $options ) || empty( $options ) )
			return;

		$str_attrs = "";

		foreach ( $attrs as $k => $v )
			$str_attrs .= " {$k}=\"{$v}\"";

		$output = '<div class="checkbox">';
		foreach ( $options as $value => $label )
		{
			$is_checked = in_array( $value, (array) $checked ) ? 'checked' : '';
			
			$output .= "<label>";
			$output .= "<input name='{$name}[]' type='checkbox' value='{$value}' $is_checked> $label ";
			$output .= "</label>";
		}

		echo $output . '</div>';
	}

	public static function selectWeekDay( $name )
	{

	}

	public static function selectMonth( $name )
	{

	}

	public static function radio()
	{

	}
}