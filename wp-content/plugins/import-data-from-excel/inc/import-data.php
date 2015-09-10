<?php
if( !class_exists( 'Fitwp_Import_Data' ) )
{
	/**
	 *
	 */
	class Fitwp_Import_Data
	{
		/**
		 * Class constructor
		 * Add hooks to ASQ
		 */
		function __construct()
		{
			// Reading file and update posts
			add_action( 'load-tools_page_import-excel-data', array( $this, 'import_companies' ) );
		}

		/**
		 * Process parse file data and update posts.
		 *
		 *
		 * @return void
		 */
		function import_companies()
		{
			if( isset( $_FILES['file_data'] ) )
			{
				$xlsx = new SimpleXLSX( $_FILES['file_data']['tmp_name'] );
				list($cols,) = $xlsx->dimension();

				$keys_meta = array(
					'post_title'	=>	1,
					'address'		=>	2,
					'address2'		=>	3,
					'area'			=>	4,
					'city'			=>	5,
					'postcode'		=>	6,
					'state'			=>	7,
					'website'		=>	8,
					'email'			=>	9,
					'phone'			=>	10,
					'facebook'		=>	11,
					'twitter'		=>	12,
					'googleplus'	=>	13,
					'pinterest'		=>	14,
					'linkedin'		=>	15,
					'instagram'		=>	16,
					'rss'			=>	17,
				);

				foreach( $xlsx->rows() as $k => $r )
				{
					if ($k == 0) continue; // skip first row

					// get post_id in current row
					$post_id =	$r[0];
					// if post doesn't exist
					if ( empty( $post_id ) )
					{
						$args_post = array(
							'post_title'    =>  $r[1],
							'post_type'     =>  'company',
							'post_status'   =>  'publish',
							'post_author'   =>  681,    // assign new company to user No Owner
						);
						$post_id = wp_insert_post( $args_post );
					}

					foreach( $keys_meta as $key => $value )
					{
						if( check_post_exists( $post_id ) )
						{
							// check cell value
							if( isset( $r[$value] ) )
							{
								if( $key == 'post_title' )
								{
									$args = array(
										'ID'			=>	$post_id,
										'post_title'	=>	$r[$value],
									);
									wp_update_post( $args );
								}
								else
								{
									update_post_meta( $post_id, $key, $r[$value] );
								}
							}
						}
					}
				}
			}
		}

	}

	new Fitwp_Import_Data;
}