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
