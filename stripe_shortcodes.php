<?php
/*--------------------------------------------------------------
# Save billing information on database Change the ID form on gform_after_submission_ID
--------------------------------------------------------------*/
add_action("gform_after_submission_7", "activate_subscription_employer", 10, 2);

function activate_subscription_employer ($entry, $form) {
	//save the entry id to the current users meta when they subscribe for premium stuff
	update_user_meta($entry['created_by'], 'snp_entry_id', $entry['id']);
	
	//Save billing information
	if( isset(update_user_meta(get_current_user_id(), '_address1', $entry['5.1']) ));
	if(isset(update_user_meta( get_current_user_id(), '_address2', $entry['5.2']) ));
	if(isset(update_user_meta( get_current_user_id(), '_city', $entry['5.3']) ));
	if(isset(update_user_meta( get_current_user_id(), '_state', $entry['5.4']) ));
	if(isset(update_user_meta( get_current_user_id(), '_zipcode', $entry['5.5']) ));
	if(isset(update_user_meta( get_current_user_id(), '_country', $entry['5.6']) ));
}

/*--------------------------------------------------------------
# Unsubscription and table function and shortcode
--------------------------------------------------------------*/
function get_all_subscriptions($id, $user_id = null) {
	$id=7;
	$search_criteria2['field_filters'][] = array( 'key' => 'created_by', 'value' => $user_id );			
	$sorting = array();
	$paging = array( 'offset' => 0, 'page_size' => 30 );
	$form = $id;
	$form_entries = GFAPI::get_entries( $form, $search_criteria2, $sorting, $paging );
	$results = array();

	foreach ($form_entries as $entry){
		$result = array();
		$feed = gf_stripe()->get_payment_feed( $entry );
		$subscription_name = $feed['meta']['subscription_name'];
		$entry_id = $entry['id'];
		$status = $entry['payment_status'];
		$amount = number_format(floatval($entry['payment_amount']), 2) . "$ / " . $feed['meta']['billingCycle_unit'];
		$start_date = strtotime($entry['date_created']);
		$format_date = date('m/d/y', $start_date);

		$length = $feed['meta']['billingCycle_length']." ".$feed['meta']['billingCycle_unit'];
			$result['lit_entry_id'] = $entry_id;
			$result['lit_subscription_name'] = $subscription_name;
			$result['lit_date'] = $format_date;
			$result['lit_amount'] = $amount;
			$result['lit_status'] = $status;
			
			$results[$entry_id] = $result;
	}
	return  $results;
}


/*--------------------------------------------------------------
# Shortcode
--------------------------------------------------------------*/
function get_all_subscriptions_shortcode($id = 0){
		

		ob_start();
		
		$results = get_all_subscriptions($id, get_current_user_id());
		$status = get_user_meta( get_current_user_id(), '_subscription_status', true );
		
		wp_enqueue_style('stripe_1_css', get_stylesheet_directory_uri() . '/styles/stripe/jquery.dataTables.min.css');
		wp_enqueue_style('stripe_2_css', get_stylesheet_directory_uri() . '/styles/stripe/responsive.dataTables.min.css');
		wp_enqueue_style('stripe_3_css', get_stylesheet_directory_uri() . '/styles/stripe/dataTables.bootstrap.css');
		wp_enqueue_style('stripe_4_css', get_stylesheet_directory_uri() . '/styles/stripe/custom.css');
		
		wp_enqueue_script('stripe_1_js', get_stylesheet_directory_uri() . '/js/stripe/jquery.dataTables.min.js', array('jquery') );
		wp_enqueue_script('stripe_2_js', get_stylesheet_directory_uri() . '/js/stripe/dataTables.responsive.min.js', array('jquery') );
		wp_enqueue_script('stripe_3_js', get_stylesheet_directory_uri() . '/js/stripe/custom.js', array('jquery') );

		if ($status == 'Active'){
			echo  '<a class="cancel_subscription_popup" style="border-bottom: 2px solid !important; width: 100%; display: block; margin: 20px 0px;" href="#">Cancel my subscription</a>';
			echo 'Your Employer subscription is active or subscription period not end';
		} else{
			echo '<div class="upgrade_button">
			<a class="subscription-employer-pro" href="#" style="border-bottom: 2px solid !important;">Change/Upgrade my role</a>
			<strong><span>Employer Pro Subscription status:</span></strong> ' . $status . 
			'</div>';
		}
		
		$html = "<div class='table-responsive'>";
			$html .= "<table id='user-subscriptions' class='table dt-responsive' cellspacing='0' width='100%'>";
			$html .= ' <thead>
				<tr class="gss_item">
					<th class="name">Subscription Name</th>
					<th class="date min-tablet-p">Start Date</th>
					<th class="amount min-tablet-p">Amount / Term</th>
					<th class="status">Status</th>
				</tr>
			</thead>
			<tbody>';

		foreach ($results as $result){
			
			$html .= "<tr class='gss_item'>";				
				$html .= "<td class='name'>".$result['lit_subscription_name']."</td>";
				$html .= "<td class='date'>".$result['lit_date']."</td>";
				$html .= "<td class='amount'>".$result['lit_amount']."</td>";
				$html .= "<td class='status '>".$result['lit_status']."</td>";
			$html .= "</tr>";
		}
		$html .= "</tbody></table></div>";
		echo $html;
				
	return ob_get_clean();
}

add_shortcode('get_all_subscriptions_shortcode', 'get_all_subscriptions_shortcode');



