<?php namespace ASQ\Bubble;

class Bubble
{
	public static $settings = array(
		'enable' 		=> true,
		'amount' 		=> 10,
		'order' 	=> array(
			'key' 	=> 'date_created',
			'value'	=> 'DESC'
		),
		'transition'	=> 'slideUp',
		'trigger'		=> 'off'
	);

	public static function make()
	{
		$settings = self::$settings;

		if ( ! $settings['enable'] )
			return;

		$entries = \GFAPI::get_entries( 1, array(), $settings['order'], array( 'offset' => 0, 'per_page' => $settings['amount'] ) );

		$output = '<div class="bubble"><a class="close pull-right" href="#" title="Hide">&times;</a>';

        foreach ( $entries as $index => $entry )
        {
        	if ( $index === 0 )
        		$output .= '<div class="bubble-content">';

            $time 	= human_time_diff( strtotime( $entry['date_created'] ) );

            if ( str_contains( $time, array( 'hour', 'minute' ) ) )
            	$time = 'about ' . $time;

            $output .= "<p>{$entry['1.3']} from {$entry['17.3']}, {$entry['17.4']} asked for 3 solar panel quotes {$time} ago</p>";
       		
       		if ( $index === 0 )
       			$output .= '</div>';
        }

        echo $output . '</div>';
	}
}