<?php
/**
 * Include all files in a dir
 *
 * @param string $dir Absolute path to a dir
 */
function peace_include_dir( $dir )
{
	foreach ( glob( trailingslashit( $dir ) . '*.php' ) as $file )
	{
		include_once $file;
	}
}
