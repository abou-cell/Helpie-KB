<?php

/**
 * The main plugin file.
 *
 *
 * @link              http://helpiewp.com/
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Helpie - WordPress KB Wiki Plugin
 * Plugin URI:        http://helpiewp.com/
 * Description:       Helpie is a WordPress KB Wiki plugin.
 * Version:           1.34.1
 * Author:            HelpieWP
 * Author URI:        http://helpiewp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pauple-helpie
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (function_exists('hk_fs')) {
    hk_fs()->set_basename(true, __FILE__);
} else {
    if (!class_exists('\Helpie_Kb')) {
        class Helpie_Kb
        {
            private static $instance;
            public static function get_instance()
            {
                if (!isset(self::$instance) && !self::$instance instanceof Helpie_Kb) {
                    self::$instance = new Helpie_Kb();
                    self::$instance->init();
                }
                return self::$instance;
            }

            public static function init()
            {
                self::$instance->setup_constants();
                self::$instance->kb_activation();
                add_action('plugins_loaded', array(self::$instance, 'kb_load_textdomain'));
                require_once plugin_dir_path(__FILE__) . "/includes/lib/freemius-integrator.php";
            }

            public function setup_constants()
            {
                $constants = [
                    'HELPIE_PLUGIN_VERSION' => '1.34.1',
                    'HELPIE_PLUGIN_FILE_PATH' => __FILE__,
                    'HELPIE_PLUGIN_PATH' => plugin_dir_path(__FILE__),
                    'HELPIE_DIR_PATH' => plugin_dir_path(__FILE__) . '../',
                    'HELPIE_PLUGIN_URL' => plugin_dir_url(__FILE__),
                    'HELPIE_DOMAIN' => 'pauple-helpie',
                    'HELPIE_POST_TYPE' => 'pauple_helpie',
                    'HELPIE_TAXONOMY' => 'helpdesk_category',
                    'HELPIE_MODE' => 'live_mode',
                    'HELPIE_FOLDER_NAME' => wp_basename(__DIR__),
                ];
                foreach ($constants as $constant => $value) {
                    if (!defined($constant)) {
                        define($constant, $value);
                    }
                }
            }

            public static function kb_activation()
            {
                if (!version_compare(PHP_VERSION, '5.4', '>=')) {
                    add_action('admin_notices', [self::$instance, 'helpie_fail_php_version']);
                } elseif (!version_compare(get_bloginfo('version'), '4.5', '>=')) {
                    add_action('admin_notices', [self::$instance, 'helpie_fail_wp_version']);
                } else {
                    require_once HELPIE_PLUGIN_PATH . 'includes/plugin.php';
                }
            }

            /* Translation */
            public function kb_load_textdomain()
            {
                load_plugin_textdomain(HELPIE_DOMAIN, false, basename(dirname(__FILE__)) . '/languages');
            }

            /**
             * Show in WP Dashboard notice about the plugin is not activated (PHP version).
             * @since 1.0.0
             * @return void
             */
            public function helpie_fail_php_version()
            {
                /* translators: %s: PHP version */
                $message = sprintf(esc_html__('Helpie KB requires PHP version %s+, plugin is currently NOT ACTIVE.', HELPIE_DOMAIN), '5.4');
                $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
                echo wp_kses_post($html_message);
            }

            /**
             * Show in WP Dashboard notice about the plugin is not activated (WP version).
             * @since 1.5.0
             * @return void
             */
            public function helpie_fail_wp_version()
            {
                /* translators: %s: WP version */
                $message = sprintf(esc_html__('Helpie KB requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT ACTIVE.', HELPIE_DOMAIN), '4.5');
                $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
                echo wp_kses_post($html_message);
            }
        } // END CLASS
    }

    Helpie_Kb::get_instance();
}
