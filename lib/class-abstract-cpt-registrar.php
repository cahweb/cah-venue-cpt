<?php
/**
 * An abstract class that provides a scaffold for WordPress custom post types.
 * 
 * @author Mike W. Leavitt
 * @version 1.0.0
 */

if( !class_exists( 'WP_CPT_Registrar' ) ) {
    abstract class WP_CPT_Registrar
    {
        // Protected properties

        protected static $_text_domain;

        /**
         * You'll want to shadow/override these for your
         * implementation. This just gives you a scaffold
         * for what the code is looking for.
         */
        protected static $_labels = [
            'singular' => 'Singular Post Type',
            'plural' => 'Plural Post Types',
            'text_domain' => 'your_text_domain',
        ];
        protected static $_prefix = 'your_prefix';
        protected static $_slug = 'posttypeslug';
        protected static $_dashicon = 'dashicons-your-dashicon';

        protected static $_classname = __CLASS__;


        protected function __construct() {} // Prevents instantiation


        // Public methods

        /**
         * Registers the Studio CPT and sets related editor actions.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         *
         * @return void
         */
        public static function setup() {

            // Sets up the base labels we'll need, including establishing a custom
            // filter that end users can use to filter and/or modify things to taste
            $labels = apply_filters( static::$_prefix . '_labels', static::$_labels );

            // Register the post type
            register_post_type( static::$_slug, self::_args( $labels ));

            // Add your meta boxes
            add_action( 'add_meta_boxes', [ static::$_classname, 'register_metaboxes' ], 10, 0 );

            // Set up special save behaviors
            add_action( 'save_post_' . static::$_slug, [ static::$_classname, 'save' ], 10, 0 );
        }


        // Function to register any metaboxes you might want.
        abstract public static function register_metaboxes();

        // Save functionality for your custom metadata.
        abstract public static function save();


        // Protected methods

        /**
         * Creates, filters, and returns the array of arguments to be 
         * passed to register_post_type() in 
         * CAH_SPAStudioCPTRegistrar::register(), above.
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @param array $labels  An array of labels, defined in register(), which contain the singular label, plural label, and text domain for the CPT.
         * 
         * @return array
         */
        protected static function _args( array $labels ) : array {
            extract( $labels );

            return apply_filters( static::$_prefix . '_args', [
                'label' => __( $singular, $text_domain ),
                'description' => __( $plural, $text_domain ),
                'labels' => self::_labels( $singular, $plural, $text_domain ),
                'supports' => array( 'thumbnail', 'title', 'editor', 'custom-fields', 'page-attributes', 'post-formats' ),
                'taxonomies' => self::_taxonomies(),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'menu_icon' => static::$_dashicon,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                //'capability_type' => 'post',
            ] );
        }


        /**
         * Creates the full array of labels for our CPT, which is passed as part
         * of the $args array to register_post_type().
         * 
         * @author Mike W. Leavitt
         * @since 0.1.0
         *
         * @param string $singular      The singular label for the CPT.
         * @param string $plural        The plural label for the CPT.
         * @param string $text_domain   The text domain for the CPT.
         * 
         * @return array
         */
        protected static function _labels( string $singular, string $plural, string $text_domain ) : array {
            static::$_text_domain = $text_domain;

            return array(
                'name'                  => self::_wpstr( $plural, 'Post Type General Name' ),
                'singular_name'         => self::_wpstr( $singular, 'Post Type Singular Name'),
                'menu_name'             => self::_wpstr( $plural ),
                'name_admin_bar'        => self::_wpstr( $singular ),
                'archives'              => self::_wpstr( "$plural Archives" ),
                'parent_item_colon'     => self::_wpstr( "Parent $singular:" ),
                'all_items'             => self::_wpstr( "All $plural" ),
                'add_new_item'          => self::_wpstr( "Add New $singular" ),
                'add_new'               => self::_wpstr( "Add New" ),
                'new_item'              => self::_wpstr( "New $singular" ),
                'edit_item'             => self::_wpstr( "Edit $singular" ),
                'update_item'           => self::_wpstr( "Update $singular" ),
                'view_item'             => self::_wpstr( "View $singular" ),
                'delete_item'           => self::_wpstr( "Delete $singular" ),
                'search_items'          => self::_wpstr( "Search $plural" ),
                'not_found'             => self::_wpstr( "$singular Not Found" ),
                'not_found_in_trash'    => self::_wpstr( "$singular Not Found in Trash" ),
                'featured_image'        => self::_wpstr( "$singular Banner" ),
                'set_featured_image'    => self::_wpstr( "Set $singular Banner" ),
                'remove_featured_image' => self::_wpstr( "Remove $singular Banner" ),
                'use_featured_image'    => self::_wpstr( "Use as $singular Banner" ),
                'insert_into_item'      => self::_wpstr( "Insert into $singular" ),
                'uploaded_to_this_item' => self::_wpstr( "Uploaded to this $singular" ),
                'items_list'            => self::_wpstr( "$plural List" ),
                'items_list_navigation' => self::_wpstr( "$plural List Navigation" ),
                'filter_items_list'     => self::_wpstr( "Filter $plural List" ),
            );
        }


        /**
         * Filters the taxonomies, to be passed to _args(), above.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         *
         * @return void
         */
        protected static function _taxonomies() {
            $tax = array();
            $tax = apply_filters( static::$_prefix . 'taxonomies', $tax );

            foreach( $tax as $t ) {
                if( !taxonomy_exists( $t ) ) {
                    unset( $tax[$t] );
                }
            }

            return $tax;
        }


        /**
         * A little helper function to generate a WP localized string. 
         * This seemed cleaner than typing "$text_domain" over and 
         * over again.
         * 
         * @author Mike W. Leavitt
         * @since 1.0.0
         *
         * @param string $label  The label we're trying to localize.
         * @param string $context  The context, in case we're calling the _x() function.
         * 
         * @return string
         */
        protected static function _wpstr( string $label, string $context = null ) : string {
            if( $context ) {
                return _x( $label, $context, static::$_text_domain );
            }
            return __( $label, static::$_text_domain );
        }
    }
}
?>