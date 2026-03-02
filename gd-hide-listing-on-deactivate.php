<?php
/**
 * Plugin Name: GD – Hide Business Listings When BuddyPress Account Deactivates
 * Plugin URI: https://github.com/CapWebSolutions/gd-hide-listing-on-deactivate
 * Description: MUST USE PLUGIN: Automatically hides GeoDirectory business listings when a BuddyPress member is deactivated, and restores them when reactivated.
 * Author: Matt Ryan | Cap Web Solutions
 * Author URI: https://capwebsolutions.com
 * Version: 1.0.0
 * GitHub Plugin URI: https://github.com/CapWebSolutions/gd-hide-listing-on-deactivate
 */

// Hide listings on BuddyPress account deactivation.
add_action( 'bp-account-deactivated', 'capweb_gd_deactivate_business_listings',10 );

function capweb_gd_deactivate_business_listings( $user_id ) {
    // Check if BP Deactivate Account plugin is active and user is now inactive
    if (! function_exists('bp_account_deactivator') || ! bp_account_deactivator()->is_inactive($user_id) ) {
        return;
    }
    // if ( bp_account_deactivator()->is_inactive($user_id) ) {
    If ( WP_DEBUG ) { error_log( 'User ' . $user_id . ' deactivated by gd_deactivate_business_listings' );}
    // Deactivate all gd_place listings for this user
    $listings = get_posts(
        array(
        'post_type' => 'gd_place',
        'post_status' => 'publish',
        'author' => $user_id,
        'posts_per_page' => -1,
        'fields' => 'ids'
        ) 
    );

    if ( WP_DEBUG ) { error_log( '$listings ' . var_export( $listings, true ) );}
    if (! empty($listings) ) {
        foreach ( $listings as $listing_id ) {
            wp_update_post(
                array(
                'ID' => $listing_id,
                'post_status' => 'draft' // or 'private', 'trash' as needed
                ) 
            );
            if ( WP_DEBUG ) { error_log( 'Listing ID ' . $listing_id . ' for User ' . $user_id . ' deactivated' ); }
        }
    }
    // }
}