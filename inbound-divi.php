<?php
/*
Plugin Name: Inbound Extension - Divi theme support
Plugin URI: http://www.inboundnow.com/
Description: Adds Divi Page Builder features to the landing page custom post type. Only supports 'Current Theme' as template.
Version: 1.0.1
Author: Inbound Now
Contributors: Hudson Atwell
Author URI: http://www.inboundnow.com/
*/


if (!class_exists('Inbound_DIVI')) {


    class Inbound_DIVI {

        static $map;
        static $settings;

        /**
         *  Initialize class
         */
        public function __construct() {
            self::define_constants();
            self::load_hooks();
        }


        /**
         *  Define constants
         */
        public static function define_constants() {
            define('INBOUND_DIVI_CURRENT_VERSION', '1.0.1');
            define('INBOUND_DIVI_LABEL', __('Divi Integration', 'inbound-pro'));
            define('INBOUND_DIVI_SLUG', 'inbound-divi');
            define('INBOUND_DIVI_FILE', __FILE__);
            define('INBOUND_DIVI_REMOTE_ITEM_NAME', 'inbound-divi');
            define('INBOUND_DIVI_PATH', realpath(dirname(__FILE__)) . '/');
            $upload_dir = wp_upload_dir();
            $url = (!strstr(INBOUND_DIVI_PATH, 'plugins')) ? $upload_dir['baseurl'] . '/inbound-pro/extensions/' . plugin_basename(basename(__DIR__)) . '/' : WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/';
            define('INBOUND_DIVI_URLPATH', $url);
        }

        /**
         * Load Hooks & Filters
         */
        public static function load_hooks() {

            /* WP-Admin Only */
            if (is_admin()) {
                /* support post types */
                add_filter('et_builder_post_types', array(__CLASS__, 'support_post_types'));
                add_filter('et_fb_post_types', array(__CLASS__, 'support_post_types'));

                /* add divi settings metabox */
                add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));

                /* adds supporting js */
                add_action('admin_head', array(__CLASS__, 'add_admin_js'));
            }
            
        }

        /**
         *  Adds custom post type support for 'landing-page' to Divi theme.
         */
        public static function support_post_types($post_types) {
            $post_types[] = 'landing-page';
            return $post_types;
        }

        /**
         *  Adds Divi Settings to Landing Pages
         */
        public static function add_meta_boxes() {
            global $post;

            $template = Landing_Pages_Variations::get_current_template($post->ID);

            if ($template != 'default') {
                return;
            }

            add_meta_box('et_settings_meta_box', __('Divi Settings', 'Divi'), 'et_single_settings_meta_box', 'landing-page', 'side', 'high');

        }

        public static function add_admin_js() {
            global $post;


            $s = get_current_screen();
            if (!isset($s) || empty($s->post_type) || $s->post_type != 'landing-page') {
                return;
            }

            if (!isset($post->ID)) {
                return;
            }

            $template = Landing_Pages_Variations::get_current_template($post->ID);

            if ($template == 'default') {

                ?>
                <script>
                    jQuery(function ($) {
                        jQuery('#et_pb_layout').insertAfter(jQuery('#et_pb_main_editor_wrap'));
                    });
                </script>
                <?php
            } else {
                ?>
                <script>
                    jQuery(function ($) {
                        jQuery('.et_pb_toggle_builder_wrapper').hide();
                    });
                </script>
                <?php
            }

        }
    }


    new Inbound_DIVI();

}
