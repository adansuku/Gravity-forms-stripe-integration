<?php
	
/* ################ stripe shortocodes ################################# */

require_once( trailingslashit (get_stylesheet_directory() ). '/stripe/stripe_shortcodes.php');
	

/*----------------------------------------------------------------------------------------------------------------------
# Start stripe logic here --
----------------------------------------------------------------------------------------------------------------------*/

function pwm_after_payment( $entry, $action ) {
	$entry_id = rgar( $entry, 'id' );
	//if user is logged in save the stripe customer id for new subscriptions and save more info to users meta
	if ( rgar( $action, 'type' ) == 'create_subscription' ) {
		$stripe_customer_id = rgar( $action, 'customer_id' );
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			//add new customer id if user doesn't have one
			if ( get_user_meta( $user_id, 'stripe_customer_id', true ) == '' ) {
				update_user_meta( $user_id, 'stripe_customer_id', $stripe_customer_id );
			}
			pwm_add_subscription( $entry_id, $user_id );			
		}
		//if user is not logged in we will need to get the user id from user registered hook. not sure if that runs
		//before this. So just save the stripe ID from this hook to use in that hook.
		gform_update_meta( $entry_id, 'pwm_stripe_customer_id', $stripe_customer_id );
	}
}
add_action( 'gform_post_payment_action', 'pwm_after_payment', 10, 2 );




/**
 * @param $entry_id
 * @param int $user_id
 *
 * Saves the subscription id for cancellation later, along with some useful dates, and the user id to gform meta
 * set to run after we have both customer id and a user id
 * */
function pwm_add_subscription( $entry_id, $user_id = 0 ) {
	
	if ( ! $user_id && is_user_logged_in() ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id && ! is_user_logged_in() ) {
		return; //we cant save anything
	}

	//once we have user_id and entry_id we can save info to the user for later use
	update_user_meta( $user_id, 'pwm_subscription_entry_id', $entry_id );
	update_user_meta( $user_id, 'pwm_subscribed_before', true );
	update_user_meta( $user_id, 'pwm_subscribe_date', date( 'Y-m-d' ) ); //good to have :)
	update_user_meta( $user_id, '_subscription_status', "Active" );

	//when cancelling payment we dont know who were deleting from! sometimes there is $entry['created_by'] sometimes not. This is for sure.
	gform_update_meta( $entry_id, 'pwm_subscribers_user_id', $user_id );

	//upgrade user
	pwm_set_user_role( $user_id, 'employer-recruiter-50');
	
}


/**
 * Cancel at period end AND dont run the callback the first time when this runs via the subscribe or renew feed
 */
add_filter( 'gform_stripe_subscription_cancel_at_period_end', 'stripe_subscription_cancel_at_period_end', 10, 3 );
function stripe_subscription_cancel_at_period_end( $at_period_end, $entry, $feed ) {
	
	$feed_name = rgars( $feed, 'meta/feedName' );
	if ( $feed_name == 'Subscribe' || $feed_name == 'Renew' ) {
		//don't run the cancellation hook unless its cancelling!
		remove_action( 'gform_post_payment_callback', 'cancelling_at_period_end' );
		return true;
	}
	//return $at_period_end;
	return true;
}

/**
 * @param $entry
 * @param $action
 * @param $result
 *
 * Runs at actual period end. remove paid privileges and other stuff
 */
function cancelling_at_period_end( $entry, $action, $result ) {
	if ( ! $result && rgar( $action, 'type' ) == 'cancel_subscription' && strtolower( $entry['payment_status'] ) == 'cancelled'	) {
		//end of subscription has come.
		$entry_id = rgar( $entry, 'id' );
		$user_id = gform_get_meta( $entry_id, 'pwm_subscribers_user_id' );		
		pwm_remove_subscription($entry_id, $user_id);
	}
}
add_action( 'gform_post_payment_callback', 'cancelling_at_period_end', 10, 3 );


/**
 * @param $entry_id
 * @param $user_id
 * Anything you want when the subscription truly ends
 */
function pwm_remove_subscription($entry_id, $user_id ){
	update_user_meta( $user_id, 'pwm_subscribed_till_end', '' );
	//downgrade role if necessary. make sure its not an admin!
	pwm_remove_user_role( $user_id,  'employer-recruiter-50' );
}


/**
 * get the right customer id if logged in when making a payment, if exists, dont make a new one
 */
add_filter( 'gform_stripe_customer_id', 'get_stripe_customer_id', 10, 4 );
function get_stripe_customer_id( $customer_id, $feed, $entry, $form ) {
	$feed_name = rgars( $feed, 'meta/feedName' );

	if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'stripe_customer_id', true ) != '' ) {
		$customer_id = get_user_meta( get_current_user_id(), 'stripe_customer_id', true );
	}

	return $customer_id;
}