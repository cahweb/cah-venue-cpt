<?php
/**
 * Plugin Name: CAH Venue CPT (SPA)
 * Description: A custom post type for displaying the various venues in the UCF School of Performing Arts.
 * Author: Mike W. Leavitt
 * Version: 0.1.0
 */
defined( 'ABSPATH' ) or die( "No direct access plzthx." );

define( 'CAH_SPA_VENUE__VERSION', '0.1.0' );
define( 'CAH_SPA_VENUE__PLUGIN_FILE', __FILE__ );
define( 'CAH_SPA_VENUE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAH_SPA_VENUE__PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

require_once 'includes/cah-spa-venue-setup.php';
//require_once 'includes/cah-spa-venue-editor.php';

register_activation_hook( __FILE__, function() {
    CAH_SPAVenueSetup::setup();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );

add_action( 'init', [ 'CAH_SPAVenueSetup', 'setup' ], 10, 0 );
?>