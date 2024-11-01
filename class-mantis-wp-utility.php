<?php

/**
 * Plugin Name: WP Utility and Performance
 * Plugin URI: https://wordpress.org/plugins/wp-utility-and-performance/
 * Version: 1.0.2
 * Author: Mayank Majeji, ThemeMantis
 * Author URI: https://thememantis.com/
 * Description: Allows you to remove unused resources and improve speed and performance of your WordPress website
 * License: GPLv2
 * Text Domain: wp-utility-and-performance
 * Domain Path: languages
 *
 * WP Utility is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Utility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Utility. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WP Utility & Performance Plugin
 * @author Mayank Majeji,ThemeMantis
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('WPUP_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WPUP_PLUGIN_URI', plugins_url('', __FILE__));
define('WPUP_PLUGIN_VERSION', '1.0.0');

/**
 * WP_Utility_And_Performance
 *
 * @package    WP_Utility_And_Performance
 * @author     Mayank Majeji, ThemeMantis
 */
if (!class_exists('WP_Utility_And_Performance')) {

    class WP_Utility_And_Performance
    {

        // Plugin Variables
        var $plugin_name;
        var $plugin_display_name;
        var $db_welcome_dismissed_key;

        // Plugin Initialization
        public function __construct()
        {

            // Variable Initialisation
            $this->plugin_name = 'wp-utility-and-performance';
            $this->plugin_display_name = 'WP Utility & Performance';
            $this->db_welcome_dismissed_key = $this->plugin_name . '_welcome_dismissed_key';

            // Admin Hooks
            add_action('admin_init', array(&$this, 'wpup_register_settings'));
            add_action('admin_menu', array(&$this, 'wpup_register_menu'));
            add_action('admin_notices', array(&$this, 'wpup_dashboard_notices'));
            add_action('wp_ajax_' . $this->plugin_name . '_dismiss_dashboard_notices', array(&$this, 'wpup_dismiss_dashboard_notices'));

            // Hooks
            add_action('admin_enqueue_scripts', array(&$this, 'wpup_assets'));
            add_action('init', array(&$this, 'wpup_actions_filters'));

            // WP Emojis
            if (get_option('wpup_wp_emojis')) {
                add_action('init', array($this, 'wpup_wp_emojis'));
            }
        }

        // Register Settings
        function wpup_register_settings()
        {

            register_setting($this->plugin_name, 'wpup_css_files_version');
            register_setting($this->plugin_name, 'wpup_js_files_version');
            register_setting($this->plugin_name, 'wpup_rsd_link');
            register_setting($this->plugin_name, 'wpup_wp_version_generator_tag');
            register_setting($this->plugin_name, 'wpup_wlwmanifest_link');
            register_setting($this->plugin_name, 'wpup_shortlink');
            register_setting($this->plugin_name, 'wpup_xml_rpc');
            register_setting($this->plugin_name, 'wpup_plugin_autoupdate');
            register_setting($this->plugin_name, 'wpup_theme_autoupdate');
            register_setting($this->plugin_name, 'wpup_dashicons');
            register_setting($this->plugin_name, 'wpup_wp_embed');
            register_setting($this->plugin_name, 'wpup_feature_image_to_rss_feed');
            register_setting($this->plugin_name, 'wpup_rss_feed');
            register_setting($this->plugin_name, 'wpup_rss_feed_links');
            register_setting($this->plugin_name, 'wpup_enable_shortcode_widget');
        }

        // Show relevant dashboard notices for the plugin
        function wpup_dashboard_notices()
        {
            global $pagenow;

            if (!get_option($this->db_welcome_dismissed_key)) {
                if (!($pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == $this->plugin_name)) {
                    $setting_page = admin_url('options-general.php?page=' . $this->plugin_name);

                    // load admin notices
                    include_once(WPUP_PLUGIN_DIR . '/admin/dashboard-notices.php');
                }
            }
        }

        // Dismiss the welcome dashboard notice for the plugin
        function wpup_dismiss_dashboard_notices()
        {
            check_ajax_referer($this->plugin_name . '-nonce', 'nonce');

            // user has dismissed the welcome notice
            update_option($this->db_welcome_dismissed_key, 1);
            exit;
        }

        // Register the plugin settings panel
        function wpup_register_menu()
        {
            add_submenu_page('options-general.php', $this->plugin_display_name, $this->plugin_display_name, 'manage_options', $this->plugin_name, array(&$this, 'wpup_settings_page'));
        }

        /**
         * Output the Administration Panel
         * Save POSTed data from the Administration Panel into a WordPress option
         */
        function wpup_settings_page()
        {

            // Only admin user can access this page
            if (!current_user_can('administrator')) {
                echo '<p>' . esc_html__('Sorry, you are not allowed to access this page.', 'wp-utility-and-performance') . '</p>';
                return;
            }

            // Save Settings
            if (isset($_REQUEST['submit'])) {

                // Nounce check
                if (!isset($_REQUEST[$this->plugin_name . '_nonce'])) {

                    // Missing nonce
                    $this->errorMessage = esc_html__('nonce field is missing. Settings NOT saved.', 'wp-utility-and-performance');
                } elseif (!wp_verify_nonce($_REQUEST[$this->plugin_name . '_nonce'], $this->plugin_name)) {

                    // Invalid nonce
                    $this->errorMessage = esc_html__('Invalid nonce specified. Settings NOT saved.', 'wp-utility-and-performance');
                } else {
                    // Save
                    // $_REQUEST has already been slashed by wp_magic_quotes in wp-settings
                    // so do nothing before saving
                    update_option('wpup_css_files_version', isset($_REQUEST['wpup_css_files_version']) ? sanitize_text_field($_REQUEST['wpup_css_files_version']) : 0);
                    update_option('wpup_js_files_version', isset($_REQUEST['wpup_js_files_version']) ? sanitize_text_field($_REQUEST['wpup_js_files_version']) : 0);
                    update_option('wpup_rsd_link', isset($_REQUEST['wpup_rsd_link']) ? sanitize_text_field($_REQUEST['wpup_rsd_link']) : 0);
                    update_option('wpup_wp_version_generator_tag', isset($_REQUEST['wpup_wp_version_generator_tag']) ? sanitize_text_field($_REQUEST['wpup_wp_version_generator_tag']) : 0);
                    update_option('wpup_wlwmanifest_link', isset($_REQUEST['wpup_wlwmanifest_link']) ? sanitize_text_field($_REQUEST['wpup_wlwmanifest_link']) : 0);
                    update_option('wpup_shortlink', isset($_REQUEST['wpup_shortlink']) ? sanitize_text_field($_REQUEST['wpup_shortlink']) : 0);
                    update_option('wpup_xml_rpc', isset($_REQUEST['wpup_xml_rpc']) ? sanitize_text_field($_REQUEST['wpup_xml_rpc']) : 0);
                    update_option('wpup_plugin_autoupdate', isset($_REQUEST['wpup_plugin_autoupdate']) ? sanitize_text_field($_REQUEST['wpup_plugin_autoupdate']) : 0);
                    update_option('wpup_theme_autoupdate', isset($_REQUEST['wpup_theme_autoupdate']) ? sanitize_text_field($_REQUEST['wpup_theme_autoupdate']) : 0);
                    update_option('wpup_dashicons', isset($_REQUEST['wpup_dashicons']) ? sanitize_text_field($_REQUEST['wpup_dashicons']) : 0);
                    update_option('wpup_wp_embed', isset($_REQUEST['wpup_wp_embed']) ? sanitize_text_field($_REQUEST['wpup_wp_embed']) : 0);
                    update_option('wpup_feature_image_to_rss_feed', isset($_REQUEST['wpup_feature_image_to_rss_feed']) ? sanitize_text_field($_REQUEST['wpup_feature_image_to_rss_feed']) : 0);
                    update_option('wpup_wp_emojis', isset($_REQUEST['wpup_wp_emojis']) ? sanitize_text_field($_REQUEST['wpup_wp_emojis']) : 0);
                    update_option('wpup_rss_feed', isset($_REQUEST['wpup_rss_feed']) ? sanitize_text_field($_REQUEST['wpup_rss_feed']) : 0);
                    update_option('wpup_rss_feed_links', isset($_REQUEST['wpup_rss_feed_links']) ? sanitize_text_field($_REQUEST['wpup_rss_feed_links']) : 0);
                    update_option('wpup_enable_shortcode_widget', isset($_REQUEST['wpup_enable_shortcode_widget']) ? sanitize_text_field($_REQUEST['wpup_enable_shortcode_widget']) : 0);

                    // update welcome dismiss key
                    update_option($this->db_welcome_dismissed_key, 1);

                    // display settings saved message
                    $this->message = esc_html__('Settings Saved.', 'wp-utility-and-performance');
                }
            }

            // Get current settings
            $this->settings = array(
                'wpup_css_files_version' => esc_html(wp_unslash(get_option('wpup_css_files_version'))),
                'wpup_js_files_version' => esc_html(wp_unslash(get_option('wpup_js_files_version'))),
                'wpup_rsd_link' => esc_html(wp_unslash(get_option('wpup_rsd_link'))),
                'wpup_wp_version_generator_tag' => esc_html(wp_unslash(get_option('wpup_wp_version_generator_tag'))),
                'wpup_wlwmanifest_link' => esc_html(wp_unslash(get_option('wpup_wlwmanifest_link'))),
                'wpup_shortlink' => esc_html(wp_unslash(get_option('wpup_shortlink'))),
                'wpup_xml_rpc' => esc_html(wp_unslash(get_option('wpup_xml_rpc'))),
                'wpup_plugin_autoupdate' => esc_html(wp_unslash(get_option('wpup_plugin_autoupdate'))),
                'wpup_theme_autoupdate' => esc_html(wp_unslash(get_option('wpup_theme_autoupdate'))),
                'wpup_dashicons' => esc_html(wp_unslash(get_option('wpup_dashicons'))),
                'wpup_wp_embed' => esc_html(wp_unslash(get_option('wpup_wp_embed'))),
                'wpup_feature_image_to_rss_feed' => esc_html(wp_unslash(get_option('wpup_feature_image_to_rss_feed'))),
                'wpup_wp_emojis' => esc_html(wp_unslash(get_option('wpup_wp_emojis'))),
                'wpup_rss_feed' => esc_html(wp_unslash(get_option('wpup_rss_feed'))),
                'wpup_rss_feed_links' => esc_html(wp_unslash(get_option('wpup_rss_feed_links'))),
                'wpup_enable_shortcode_widget' => esc_html(wp_unslash(get_option('wpup_enable_shortcode_widget'))),
            );

            // Load Settings Form
            include_once(WPUP_PLUGIN_DIR . 'admin/settings.php');
        }

        // Enqueue Plugin Assets
        public function wpup_assets()
        {
            // Enqueue admin styles
            wp_enqueue_style('wp-utility-admin-style', WPUP_PLUGIN_URI . '/assets/css/wpup-admin-style.css', array(), (WPUP_PLUGIN_URI . 'assets/css/wpup-admin-style.css'), false);
        }

        // Disable CSS & JS Version
        public function wpup_remove_cssjs_ver($src)
        {
            if (strpos($src, '?ver='))
                $src = remove_query_arg('ver', $src);
            return $src;
        }

        // Disable Remove Shortlink
        public function wpup_remove_redundant_shortlink()
        {
            // remove HTML meta tag
            // <link rel='shortlink' href='http://example.com/?p=25' />
            remove_action('wp_head', 'wp_shortlink_wp_head', 10);

            // remove HTTP header
            // Link: <https://example.com/?p=25>; rel=shortlink
            remove_action('template_redirect', 'wp_shortlink_header', 11);
        }

        // Dashicons in frontend to non-admin
        public function wpup_dashicons()
        {
            if (current_user_can('update_core')) {
                return;
            }
            wp_deregister_style('dashicons');
        }

        // Disable WP Embed
        public function wpup_wp_embed()
        {
            wp_deregister_script('wp-embed');
        }

        // Add Feature Image to RSS Feed
        public function wpup_feature_image_to_RSS_feed($content)
        {
            global $post;
            if (has_post_thumbnail($post->ID)) {
                $content = '' . get_the_post_thumbnail($post->ID, 'thumbnail', array('style' => 'float:left; margin:0 15px 15px 0;')) . '' . $content;
            }
            return $content;
        }

        // Disable WP Emoji
        public function wpup_wp_emojis()
        {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

            // Remove from TinyMCE
            add_filter('tiny_mce_plugins', array($this, 'wpup_disable_emojis_tinymce'));
            add_filter('wp_resource_hints', array($this, 'wpup_disable_emojis_remove_dns_prefetch'), 10, 2);
        }

        // remove the tinymce emoji plugin
        public function wpup_disable_emojis_tinymce($plugins)
        {
            if (is_array($plugins)) {
                return array_diff($plugins, array('wpemoji'));
            }

            return array();
        }

        // Remove emoji CDN hostname from DNS prefetching hints
        public function wpup_disable_emojis_remove_dns_prefetch($urls, $relation_type)
        {
            if ('dns-prefetch' == $relation_type) {
                /** This filter is documented in wp-includes/formatting.php */
                $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

                $urls = array_diff($urls, array($emoji_svg_url));
            }

            return $urls;
        }

        // Disable RSS Feed Messsage
        public function wpup_disable_rss_feed()
        {
            wp_die(__('No feed available, please visit the', 'wp-utility-and-performance') . '<a href="' . esc_url(home_url('/')) . '">' . __('homepage', 'wp-utility-and-performance') . '</a>!');
        }

        // Actions & Filters
        public function wpup_actions_filters()
        {

            // CSS Version
            if (get_option('wpup_css_files_version')) {
                add_filter('style_loader_src', array($this, 'wpup_remove_cssjs_ver'), 10, 2);
            }

            // JS Version
            if (get_option('wpup_js_files_version')) {
                add_filter('script_loader_src', array($this, 'wpup_remove_cssjs_ver'), 10, 2);
            }

            // RSD Link
            if (get_option('wpup_rsd_link')) {
                remove_action('wp_head', 'rsd_link');
            }

            // WordPress Version Generator Tag
            if (get_option('wpup_wp_version_generator_tag')) {
                remove_action('wp_head', 'wp_generator');
            }

            // WLW Manifest Link
            if (get_option('wpup_wlwmanifest_link')) {
                remove_action('wp_head', 'wlwmanifest_link');
            }

            // Shortlink
            if (get_option('wpup_shortlink')) {
                add_filter('after_setup_theme', array($this, 'wpup_remove_redundant_shortlink'));
            }

            // XML RPC
            if (get_option('wpup_xml_rpc')) {
                add_filter('xmlrpc_enabled', '__return_false');
            }

            // Auto Update For Plugins
            if (get_option('wpup_plugin_autoupdate')) {
                add_filter('auto_update_plugin', '__return_true');
            }

            // Auto Update For Themes
            if (get_option('wpup_theme_autoupdate')) {
                add_filter('auto_update_theme', '__return_true');
            }

            // Dashicons
            if (get_option('wpup_dashicons')) {
                add_action('wp_enqueue_scripts', array($this, 'wpup_dashicons'));
            }

            // WP Embeds
            if (get_option('wpup_wp_embed')) {
                add_action('wp_footer', array($this, 'wpup_wp_embed'));
            }

            // RSS Feeds
            if (get_option('wpup_rss_feed')) {
                add_action('do_feed', array($this, 'wpup_disable_rss_feed'), 1);
                add_action('do_feed_rdf', array($this, 'wpup_disable_rss_feed'), 1);
                add_action('do_feed_rss', array($this, 'wpup_disable_rss_feed'), 1);
                add_action('do_feed_rss2', array($this, 'wpup_disable_rss_feed'), 1);
                add_action('do_feed_atom', array($this, 'wpup_disable_rss_feed'), 1);
                add_action('do_feed_rss2_comments', array($this, 'wpup_disable_rss_feed'), 1);
                add_action('do_feed_atom_comments', array($this, 'wpup_disable_rss_feed'), 1);
            }

            // RSS Feed Links
            if (get_option('wpup_rss_feed_links')) {
                remove_action('wp_head', 'feed_links_extra', 3);
                remove_action('wp_head', 'feed_links', 2);
            }

            // Add Feature Image to RSS Feed
            if (get_option('wpup_feature_image_to_rss_feed')) {
                add_filter('the_excerpt_rss', array($this, 'wpup_feature_image_to_RSS_feed'));
                add_filter('the_content_feed', array($this, 'wpup_feature_image_to_RSS_feed'));
            }

            // Shortcode in Widget
            if (get_option('wpup_enable_shortcode_widget')) {
                add_filter('widget_text', 'do_shortcode');
            }
        }
    }
}

new WP_Utility_And_Performance();
