<?php

global $wpdb;

$data = [];

foreach ( ['company_name', 'membership', 'leads_enable', 'state', 'leads_manually_suspend'] as $input )
{
	$data[$input] = ( isset( $_GET[$input] ) ) ? trim( $_GET[$input] ) : '';
}

$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;

$users = [];
// Get all user with membership if membership is not empty
if ( ! empty( $data['membership'] ) )
	$users = $wpdb->get_col("SELECT user_id FROM asq_usermeta WHERE meta_key = 'membership' AND meta_value = '{$data['membership']}'");

$query_args = ['post_type' => 'company'];

if ( ! empty( $paged) )
	$query_args['paged'] = $paged;

if ( ! empty( $data['company_name'] ) )
{
	$query_args['s'] = $data['company_name'];
}

if ( ! empty( $users ) )
{
	$query_args['meta_query'][] = [
		'key' 		=> 'user',
		'value' 	=> implode( ',', $users ),
		'compare' 	=> 'IN'
	];
}

if ( ! empty( $data['state'] ) )
{
	$query_args['meta_query'][] = [
		'key' => 'state',
		'value' => $data['state']
	];
}

if ( ! empty( $data['leads_enable'] ) )
{
	$query_args['meta_query'][] = [
		'key' 	=> 'leads_enable',
		'value' => $data['leads_enable']
	];
}

if ( ! empty( $data['leads_manually_suspend'] ) )
{
	$query_args['meta_query'][] = [
		'key' 	=> 'leads_manually_suspend',
		'value' => $data['leads_manually_suspend']
	];
}

$loop = new WP_Query( $query_args );

if ( ! $loop->have_posts() && ! empty( $query_args['s'] ) ) {
	unset( $query_args['s'] );
	
	$query_args['meta_query'][] = [
		'key' 	=> 'accounting_number',
		'value' => $data['company_name']
	];

	$loop = new WP_Query( $query_args );
}
?>
<h2>Companies Search</h2>

<form>
	<input type="text" name="company_name" value="<?php echo $data['company_name'] ?>" placeholder="company name">
	<?php

	Form::select( 'membership', [
		'' 			=> 'All Membership',
		'bronze' 	=> 'Bronze',
		'silver'	=> 'Silver',
		'gold'		=> 'Gold',
		'none'		=> 'None'
	], $data['membership'] );
		
	Form::select( 'leads_enable', [
		'' => 'Purchasing Leads', 
		'1' => 'Yes', 
		'0' => 'No'
	], $data['leads_enable'] );

	Form::select( 'leads_manually_suspend', [
		'' 	=> 'Suspend Leads',
		'1' => 'Yes',
		'0' => 'No'
	], $data['leads_manually_suspend'] );

	Form::select( 'state', [
		'' 					=> 'All State',
		'Queensland' 		=> 'Queensland',
		'Victoria' 			=> 'Victoria',
		'South Australia' 	=> 'South Australia',
		'Tasmania' 			=> 'Tasmania',
		'Australian Capital Territory' 	=> 'Australian Capital Territory',
		'Western Australia'	=> 'Western Australia',
		'Northern Territory'=> 'Northern Territory',
		'New South Wales'   => 'New South Wales'
	], $data['state'] );

	?>
	<input type="hidden" name="page" value="company-search">
	<input type="submit" name="submit" value="Search" class="button">
</form>

Found: <?php echo $loop->found_posts; ?> installers  
<?php
	if ( ! empty( $data['company_name'] ) ) echo ' which title or accounting number contains: "', $data['company_name'] , '" ';
	if ( ! empty( $data['membership'] ) ) echo ' with ', $data['membership'] , ' membership ';

	if ( ! empty( $data['leads_enable'] ) ) echo ' and leads enable is ', $data['leads_enable'];
	if ( ! empty( $data['leads_manually_suspend'] ) ) echo ' and suspended is ', $data['leads_manually_suspend'];

	if ( ! empty( $data['state'] ) ) echo ' and in', $data['state'];
?>

<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<th>Name</th>
			<th>Membership</th>
			<th>Leads Activate</th>
			<th>Manually Suspend</th>
			<th>State</th>
			<th>Date Created</th>
		</tr>
	</thead>
	<tbody>

		<?php if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post(); ?>
		<tr>
			<td><a href="/wp-admin/post.php?post=<?php echo get_the_ID(); ?>&amp;action=edit"><?php echo the_title(); ?></a></td>
			<td>
			<?php 
				if ( ! empty( $data['membership'] ) ) 
				{
					echo str_title( $data['membership'] );
				}
				else 
				{
					$user = get_post_meta( get_the_ID(), 'user', true );
					if ( ! empty( $user ) && intval( $user ) > 0 )
						echo get_user_meta( $user, 'membership', true );
				}
			?>
			</td>
			<td>
				<?php
				if ( ! empty( $data['leads_enable'] ) )
					echo str_title( $data['leads_enable'] );
				else
					echo get_post_meta( get_the_ID(), 'leads_enable', true );
				?>
			</td>
			<td>
				<?php
				if ( ! empty( $data['leads_manually_suspend'] ) )
					echo str_title( $data['leads_manually_suspend'] );
				else
					echo get_post_meta( get_the_ID(), 'leads_manually_suspend', true );
				?>
			</td>
			<td><?php 
				if ( ! empty( $data['state'] ) )
					echo $data['state'];
				else
					echo get_post_meta( get_the_ID(), 'state', true );
			?>
			</td>
			<td><?php echo get_the_date(); ?></td>
		</tr>
		<?php endwhile; wp_reset_postdata(); endif; ?>
	</tbody>
</table>

<?php
$big = 999999999; // need an unlikely integer

echo paginate_links( array(
	'base'	 	=> add_query_arg('paged', '%#%'),
	'format' 	=> '&paged=%#%',
	'current' 	=> $paged,
	'total'	 	=> $loop->max_num_pages
) );
?>