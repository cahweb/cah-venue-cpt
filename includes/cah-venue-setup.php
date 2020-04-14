<?php
/**
 * Helper class for setting up the various custom post type options.
 * 
 * @author Mike W. Leavitt
 * @since 0.1.0
 */

if( !class_exists( 'WP_CPT_Registrar' ) ) { 
    require_once CAH_VENUE__PLUGIN_DIR . 'lib/class-abstract-cpt-registrar.php';
}

/*
if( !trait_exists( 'wpCustomPostTypeRegistrar', false ) ) {
    require_once CAH_VENUE__PLUGIN_DIR . 'lib/trait-cpt-registrar.php';
}
*/

if( !class_exists( 'CAH_VenueSetup' ) ) {
    class CAH_VenueSetup extends WP_CPT_Registrar
    {
        // Overriding parent class's defaults
        protected static $_labels = [
            'singular' => 'Venue',
            'plural' => 'Venues',
            'text_domain' => 'cah-spa-venue',
        ];
        protected static $_prefix = 'cah_spa_venue';
        protected static $_slug = 'venue';
        protected static $_dashicon = "dashicons-tickets-alt";

        protected static $_classname = __CLASS__;

        //use wpCustomPostTypeRegistrar;

        /**
         * Registers our custom metaboxes.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         */
        public static function register_metaboxes() {
            // The arguments here are:
            //      - the name of the metabox
            //      - the box's title in the editor
            //      - function to call for HTML markup
            //      - the post type to add the box for
            //      - situations to show the box in
            //      - priority for box display
            add_meta_box(
                'venue_directions',
                'Directions',
                [ __CLASS__, 'build_directions' ],
                'venue',
                'normal',
                'low'
            );

            add_meta_box(
                'venue_parking',
                'Parking',
                [ __CLASS__, 'build_parking' ],
                'venue',
                'normal',
                'low'
            );

            add_meta_box(
                'venue_map',
                'Map',
                [ __CLASS__, 'build_map' ],
                'venue',
                'normal',
                'low'
            );
        }


        public static function build_directions() {
            global $post;

            $dirs = get_post_meta( $post->ID, 'venue-directions', true );

            wp_editor( isset( $dirs ) ? $dirs : '', 'venue-directions', [ 'textarea_rows' => 6 ] );
        }


        public static function build_parking() {
            global $post;

            $parking = get_post_meta( $post->ID, 'venue-parking', true );

            wp_editor( isset( $parking ) ? $parking : '', 'venue-parking', [ 'textarea_rows' => 6 ] );
        }

        public static function build_map() {
            ?>
            <div class="inner-meta">
                <div class="alert alert-info">
                    <p><em>This feature is a work-in-progress, and has not yet been implemented.</em></p>
                </div>
            </div>
            <?php
        }


        /**
         * Handles saving our newly-registered metadata.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         */
        public static function save() {
            global $post;

            if( !is_object( $post ) ) return;

            if( isset( $_POST['venue-directions'] ) ) {
                update_post_meta( $post->ID, 'venue-directions', $_POST['venue-directions'] );
            }

            if( isset( $_POST['venue-parking'] ) ) {
                update_post_meta( $post->ID, 'venue-parking', $_POST['venue-directions'] );
            }
        }
    }
}
?>