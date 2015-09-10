<?php
/**
 * Template Name: CRM Frontend
 */
get_header();
//'client_care_manager', 'account'
if ( current_user_can( 'editor' ) || current_user_can('administrator') || current_user_can('author') || current_user_can('client_care_manager') || current_user_can('account') ):

	if ( ! isset ( $_GET['outcome'] ) )
		$_GET['outcome'] = 'active';
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
				$results = \ASQ\Crm\Helper::find_leads();

				$leads = $results['entries'];
				$total = $results['total_count'];

				$validate_sync = GFAPI::get_entries( 45, array(), null, array( 'offset' => 0, 'page_size' => 69 ) );
				$validate_entries = array();
				foreach( $validate_sync as $entry )
				{
					if ( in_array( $entry['status'], array( 'spam', 'trash') ) )
						continue;

					if ( $entry['is_starred'] == 0 && $entry[4] == 'Interested' )
						$validate_entries[] = $entry['id'];
				}

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
						<form method="get" action="/intranet/<?php echo $post->post_name ?>">
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
									'No answer'                 => 'No answer',
									'Unable to establish contact'  => 'Unable to establish contact'
								);

								$current_outcome = trim( $_GET['outcome'] );
								SL_Form::options( $current_outcome, $outcomes );
								?>
							</select>

							<select name="source" id="source">
								<?php
								$sources = array_merge( array( '' => 'All' ), solar_get_sources() );
								SL_Form::options( $_GET['source'], $sources );
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

				<?php if ( ! empty( $validate_entries ) ): ?>
				<section class="alert alert-warning">
					There're some leads weren't synced recently. IDs are:
					<?php foreach( $validate_entries as $id ) : ?>
					<span class="label"><?php echo $id ?></span>
					<?php endforeach; ?>
				</section>
				<?php endif; ?>

				<div class="app-controller" ng-controller="CrmController" ng-init="init()">

				<table class="table table-condensed table-hover">
					<thead>
					<tr>
						<th scope="col" class="manage-column">Action</th>
						<th scope="col" class="manage-column">ID</th>
						<th scope="col" class="manage-column">Source</th>
						<th scope="col" class="manage-column">Call Attempts</th>
						<th scope="col" class="manage-column">D/Received</th>
						<th scope="col" class="manage-column">Name</th>
						<th scope="col" class="manage-column">State</th>
						<th scope="col" class="manage-column">Phone</th>
						<th scope="col" class="manage-column">Postcode</th>
						<th scope="col" class="manage-column">Outcome</th>
						<th scope="col" class="manage-column">Email</th>
						<th scope="col" class="manage-column">Synced</th>
						<th scope="col" class="manage-column">Notes</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<th scope="col" class="manage-column">Action</th>
						<th scope="col" class="manage-column">ID</th>
						<th scope="col" class="manage-column">Source</th>
						<th scope="col" class="manage-column">Call Attempts</th>
						<th scope="col" class="manage-column">D/Received</th>
						<th scope="col" class="manage-column">Name</th>
						<th scope="col" class="manage-column">State</th>
						<th scope="col" class="manage-column">Phone</th>
						<th scope="col" class="manage-column">Postcode</th>
						<th scope="col" class="manage-column">Outcome</th>
						<th scope="col" class="manage-column">Email</th>
						<th scope="col" class="manage-column">Synced</th>
						<th scope="col" class="manage-column">Notes</th>
					</tr>
					</tfoot>

					<tbody>
					<?php
					$admin_query 	= new WP_User_Query( array( 'role' => 'Administrator' ) );
					$admins = $admin_query->get_results();
					$editor_query 	= new WP_User_Query( array( 'role' => 'Editor' ) );
					$editors = $editor_query->get_results();

					$author_query 	= new WP_User_Query( array( 'role' => 'Author' ) );
					$authors = $author_query->get_results();
					
					$ccm_query 	= new WP_User_Query( array( 'role' => 'Client Care Manager' ) );
					$ccms = $ccm_query->get_results();
					
					$account_query 	= new WP_User_Query( array( 'role' => 'Account' ) );
					$accounts = $account_query->get_results();
					

					$accepted_users = array_merge( $admins, $editors, $authors, $accounts, $ccms );
					
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
							<td>
								<?php if( current_user_can( 'manage_options' ) ): ?>
									<a href="#TB_inline?width=100%&amp;height=550&amp;inlineId=modal-call-attempt"
									   class="button thickbox"
									   ng-click="editLead($lead)"
									   title="Call Attempt">
										<i class="dashicons dashicons-microphone"></i>
									</a>
								<?php else: ?>
									<a ng-show="$lead['4']=='No answer' || $lead['4'] == 'Requested call back'"
									   href="#TB_inline?width=100%&amp;height=550&amp;inlineId=modal-call-attempt"
									   class="button thickbox"
									   ng-click="editLead($lead)"
									   title="Call Attempt">
										<i class="dashicons dashicons-microphone"></i>
									</a>
								<?php endif; ?>
							</td>
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
							<td>{{ $lead['is_starred'] }}</td>
							<td>
								<a href="#TB_inline?width=100%&amp;height=550&amp;inlineId=modal-notes"
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

				<div id="modal-call-attempt" style="display:none;">
					<div class="modal-content row-fluid">
						<div class="alert alert-info" ng-show="$message">
							{{ $message }}
						</div>

						<div class="clearfix"></div>

						<div class="span5">
							<p>
								Name: {{ $active['2.3'] + ' ' + $active['2.6'] }} <br>
								Phone: <br /> <input type="text" name="phone" ng-model="$active[9]" /><br>
								Email: <br /><input type="text" name="email" ng-model="$active[10]" />
							</p>

							<h4>Lead Outcomes *</h4>
							
							<div ng-show="$active[11] < 10">
							<?php foreach ( \ASQ\Crm\Helper::get_outcomes() as $outcome ): ?>
								<label>
									<input type="radio" ng-model="$active[4]" value="<?php echo $outcome ?>">
									<?php echo $outcome ?>
								</label>
							<?php endforeach; ?>
							</div>
							<div ng-show="$active[11] >= 10">
								<label>
									<input type="radio" ng-model="$active[4]" value="Unable to establish contact">
									Unable to establish contact
								</label>
							</div>

							<div class="conditional-fields">

								<div id="call-back" ng-show="$active[4]=='Requested call back'">
									<label for="call-back-select">Call back</label>
									<select id="call-back-select" ng-model="$active[12]" ng-options="time for time in $call_back_times"></select>
									<label for="call-back-date">Call back date</label>
									<input id="call-back-date" type="date" ng-model="$active[15]" />
								</div>

								<div id="customer-services" ng-show="$active[4]=='Already been processed'">
									<label>
										<input type="checkbox" ng-model="$active[8.1]" ng-true-value="Has been contacted by 3 installers?" ng-false-value="" /> Has been contacted by 3 installers?
									</label>
									<label>
										<input type="checkbox" ng-model="$active[8.2]" ng-true-value="Asked them to add a rating and review about their installer" ng-false-value="" /> Asked them to add a rating and review about their installer
									</label>
									<label>
										<input type="checkbox" ng-model="$active[8.3]" ng-true-value="Gave them awesome customer service" ng-false-value="" /> Gave them awesome customer service
									</label>
								</div>

								<div id="other-note">
									<h4 for="txtbx-other-note">Other Note</h4>
									<textarea id="txtbx-other-note" ng-model="$active[5]" ng-show="$active[4]!='Interested'"></textarea>
								</div>
							</div>

							<br><br>

							<a role="button" id="btn-action" class="button button-primary button-large" ng-click="updateLead($event)
					">Process</a>

						</div>

						<div class="span6">
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
									<hr />
								</div>
							</div>

							<div ng-hide="$activeNotes">
								<h4>There're no item here.</h4>
							</div>

						</div><!--.span7-->
					</div><!--.modal-content-->
				</div><!--modal-->

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
endif;
get_footer(); ?>