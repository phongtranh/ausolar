<?php
/**
 * Template Name: CRM Supplier
 */
get_header();

if ( ! is_user_logged_in() )
{
    get_template_part( 'templates/company/user-admin/form-login' );
    get_footer();
    return;
}

$wholesale = get_posts( array(
    'post_type'      => 'wholesale',
    'post_status'    => 'any',
    'posts_per_page' => 1,
    'meta_key'       => 'user',
    'meta_value'     => get_current_user_id(),
) );

if ( empty( $wholesale ) )
{
    get_template_part( 'templates/wholesale/no-company' );
    get_footer();
    return;
}

$wholesale = current( $wholesale );

$source = get_post_meta( $wholesale->ID, 'wholesale_code', true );

if ( empty ( $source ) )
{
    get_template_part( 'templates/wholesale/review' );
    get_footer();
    return;
}
?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php') ?>';
	</script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js"></script>
	<script type="text/javascript" src="<?php echo CHILD_URL ?>js/admin/crm.js"></script>

	<div id="main-wrapper" class="container">

		<article id="content">
			<div class="row-fluid">
				<div class="span12">

				<?php
				$_GET['source'] = $source;
				$_GET['start_date'] = '2014-10-01 00:00:00';

				
				$results = \ASQ\Crm\Helper::find_leads();

				$leads = $results['entries'];
				$total = $results['total_count'];

				$total_page = $total / 20;

				$current_page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

				$offset = ( $current_page - 1 ) * 20;
				?>
				<h1><?php the_title(); ?> <span class="badge"> <?php echo $total ?></span></h1>
				<style type="text/css">
					.status-green, tr.interested{
						background: #3ad07e;
						color: #fff;
					}

					.status-red, .not.interested{
						background: #E74C3C;
					}

					.status-blue{
						background: #3498DB;
					}

					.status-processed td{
						border-top: 1px solid green;
						border-bottom: 1px solid green;
					}

					.note-item{
						border: 1px solid #efefef;
						padding: 5px 10px;
					}
					#TB_window{
						min-width: 780px !important;
					}
					#TB_ajaxContent {
						min-width: 750px!important;
					}
					.table{
						border-spacing: 0;
					}
					.table th, .table td{
						text-align: left;
						padding: 8px 5px;
					}
				</style>
				<div class="app" ng-app="app">

					<div class="alignleft actions">
						<form method="get" action="/affiliates/crm">
							<?php $search = ( isset( $_GET['search'] ) ) ? trim( $_GET['search'] ) : ''; ?>
							<input type="text" name="search" id="search" placeholder="Enter term to search..." value="<?php echo $search ?>" />

							<select name="outcome" id="status">
								<?php
								$outcomes = array(
									''                       => 'All',
									'active'                    => 'Active',
									'Interested'                => 'Interested',
									'Not interested'            => 'Not interested',
									'Requested call back'       => 'Requested call back',
									'Incorrect phone number'    => 'Incorrect phone number',
									'Incorrect information'     => 'Incorrect information',
									'Already been processed'    => 'Already been processed',
									'No answer'                 => 'No answer'
								);
								$current_outcome = ( isset ( $_GET['outcome'] ) ) ? trim( $_GET['outcome'] ) : '';
								SL_Form::options( $current_outcome, $outcomes );
								?>
							</select>

							<select name="state" id="state">
								<?php
								$states = array_merge( array( '' => 'All' ), get_state_name() );
								SL_Form::options( $_GET['state'], $states)
								?>
							</select>

							<input type="submit" class="button button-primary" name="submit" value="Go">

						</form>
					</div>

					<div class="tablenav-pages">
			<span class="displaying-num">
				<?php
				printf(
					__( 'Displaying %d - %d of %d', '7listings' ),
					$offset + 1, $offset + 20,
					$total
				);
				?>
			</span>
					<?php

					$big = 99999;
					echo '<nav class="pagination">' .  paginate_links( array(
						'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format' => '?paged=%#%',
						'current' => max( 1, get_query_var('paged') ),
						'total' => $total_page + 1,
							'type' => 'list'
					) ) . '</nav>';

					?>
					</div>

				<div class="clearfix"></div>

				<div class="app-controller" ng-controller="CrmController" ng-init="init()">

				<table class="table table-condensed table-hover">
					<thead>
					<tr>
						<th scope="col" class="manage-column">ID</th>
						<th scope="col" class="manage-column">Source</th>
						<th scope="col" class="manage-column">Call Attempts</th>
						<th scope="col" class="manage-column">Date Received</th>
						<th scope="col" class="manage-column">Name</th>
						<th scope="col" class="manage-column">State</th>
						<th scope="col" class="manage-column">Phone</th>
						<th scope="col" class="manage-column">Postcode</th>
						<th scope="col" class="manage-column">Outcome</th>
						<th scope="col" class="manage-column">Email</th>
						<th scope="col" class="manage-column">Notes</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<th scope="col" class="manage-column">ID</th>
						<th scope="col" class="manage-column">Source</th>
						<th scope="col" class="manage-column">Call Attempts</th>
						<th scope="col" class="manage-column">Date Received</th>
						<th scope="col" class="manage-column">Name</th>
						<th scope="col" class="manage-column">State</th>
						<th scope="col" class="manage-column">Phone</th>
						<th scope="col" class="manage-column">Postcode</th>
						<th scope="col" class="manage-column">Outcome</th>
						<th scope="col" class="manage-column">Email</th>
						<th scope="col" class="manage-column">Notes</th>
					</tr>
					</tfoot>

					<tbody>
					<?php
					$admin_query 	= new WP_User_Query( array( 'role' => 'Administrator' ) );
					$admins = $admin_query->get_results();
					$editor_query 	= new WP_User_Query( array( 'role' => 'Editor' ) );
					$editors = $editor_query->get_results();

					$accepted_users = array_merge( $admins, $editors );
					$users = array();

					foreach( $accepted_users as $user )
					{
						$users[$user->ID] = $user->data->user_login;
					}

					if ( empty ( $leads ) ) :
						?>
						<tr>
							<td colspan="12"><?php __( "Oops! Empty here", "7listings"); ?></td>
						</tr>
					<?php
					else:
						?>
						<script type="text/javascript">
							var $leads = <?php echo json_encode( $leads ); ?>,
								$users = <?php echo json_encode( $users ); ?>,
								$current_user_id = <?php echo get_current_user_id(); ?>,
								$call_back_times = <?php echo json_encode( \ASQ\Crm\Helper::get_callback_times() ); ?>,
								$customer_services = <?php echo json_encode( \ASQ\Crm\Helper::get_customer_services()); ?>;
						</script>

						<tr ng-repeat="$lead in $leads" class="status-{{$lead['user_agent']}} {{$lead['4'] | lowercase}}">
							<td>{{ $lead['id'] }}</td>
							<td>{{ $lead[1] }}</td>
							<td>{{ $lead[11] }}</td>
							<td>{{ $lead['date_created'] }}</td>
							<td>{{ $lead['2.3'] + ' ' + $lead['2.6'] }}</td>
							<td>{{ $lead['14.4'] }}</td>
							<td>{{ $lead['9'] }}</td>
							<td>{{ $lead['14.5'] }}</td>
							<td>{{ $lead['4'] }}</td>
							<td>{{ $lead['10'] }}</td>
							<td>
								<a href="#TB_inline?width=100%&height=550&inlineId=modal-notes"
								   class="button thickbox"
								   ng-click="editLead($lead)"
								   title="Notes">
									<i class="dashicons dashicons-list-view"></i>
								</a>
							</td>
						</tr>
					<?php
					endif;
					?>
					</tbody>
				</table>

				<?php add_thickbox(); ?>

				<div id="modal-notes" style="display:none;">
					<div class="modal-content">
						<img class="modal-loading" src="<?php echo get_admin_url() ?>/images/spinner.gif" />

						<div ng-show="$activeNotes">
							<p>Number of call attempts: {{ $active['11'] }}</p>
							<p>Notes</p>

							<div class="note-box">
								<div class="note-item" ng-repeat="note in $activeNotes track by $index">
									Date: {{ note.date }} <br />
									User: {{ $users[note.user_id] }} <br />
									Outcome: {{ note.outcome }} <br />
									<p ng-bind-html="trustAsHtml(note.note)"></p>
								</div>
							</div>
						</div>

						<div ng-hide="$activeNotes">
							<h4>There're no item here.</h4>
						</div>
					</div>
				</div>

				<div class="tablenav-pages alignright">
				<span class="displaying-num">
					<?php
					printf(
						__( 'Displaying %d - %d of %d', '7listings' ),
						$offset + 1, $offset + 20,
						$total
					);
					?>
				</span>
					<?php

					$big = 99999;
					echo '<nav class="pagination">' .  paginate_links( array(
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'current' => max( 1, get_query_var('paged') ),
							'total' => $total_page + 1,
							'type' => 'list'
						) ) . '</nav>';

					?>
				</div>

				</div>
				</div><!--controller-->
				</div><!--app-->

				</div>
			</div>

		</article>

	</div>
<?php
get_footer(); ?>