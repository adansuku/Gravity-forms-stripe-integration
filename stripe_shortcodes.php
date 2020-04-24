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





