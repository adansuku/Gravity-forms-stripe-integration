<?php



/*--------------------------------------------------------------
# Unsubscription and table function and shortcode
--------------------------------------------------------------*/
function get_all_subscriptions($id, $user_id = null) {
	$id=14;
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

		$subscription_plan = get_user_meta( get_current_user_id(), '_subscription_plan', true );
		
		
	
		if($subscription_plan){ 
			echo '<div>Role acutal: ' . '<strong>' . $subscription_plan . '</strong></div>';
		}else {
			$user_role = get_current_user_id();
			$user = get_userdata($user_role);
			$user_roles=$user->roles;
				echo '<div>Role acutal: ' . '<strong>' . $user_roles[0] . '</strong></div>';
		}
		
		if ($status == false){
			echo '<div><span>Employer Pro Subscription renew status: </span> <strong>Inactive</strong></div>';
 		}

 		if ($status == true){
			echo '<div><span>Employer Pro Subscription renew status: </span> <strong>Active</strong></div>';
		} 
		
		if ($subscription_plan == 'Employer Recruiter Pro'){
			
			$date = reset($results)['lit_date'];
			$start = new DateTime($date, new DateTimeZone("UTC"));
			$month_later = clone $start;
			$month_later->add(new DateInterval("P1M"));
			echo 'Your Subscription ends: ' . '<strong>' . $month_later->format('m/d/Y') . '</strong>';
			
		}
			
		echo '<div class="upgrade_button">
				<a class="subscription-employer-pro" href="#" style="border-bottom: 2px solid !important;">Change/Upgrade my role</a>
			</div>';
				
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
		
		
		
			
		if ($status == true){
			echo '<div style="text-align: right"><a class="cancel_subscription_popup" href="#">Cancel my subscription</a></div>';
		}
		
		
				
	return ob_get_clean();
}

add_shortcode('get_all_subscriptions_shortcode', 'get_all_subscriptions_shortcode');



