<?php
/*
Plugin Name: Adventists Employment Plugin
Description: A plugin to handle multiple shortcodes for Adventists Employment site.
Version: 1.0
Author: Rohan T George
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AdventistsEmploymentPlugin
{

    public function __construct()
    {
        // Hook to initialize shortcodes
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_footer', [$this, 'custom_logout_script']);
        add_action('after_setup_theme', [$this, 'hide_admin_toolbar_for_non_admins']);
        add_action('template_redirect', [$this, 'redirect_employer_dashboard']);
    }

    /**
     * Register all shortcodes
     */
    public function register_shortcodes()
    {
        include_once plugin_dir_path(__FILE__) . 'includes/class-top-blogs.php';
        include_once plugin_dir_path(__FILE__) . 'includes/class-blog-filter.php';
        include_once plugin_dir_path(__FILE__) . 'includes/class-related-posts.php';
        include_once plugin_dir_path(__FILE__) . 'includes/class-job-form.php';
        include_once plugin_dir_path(__FILE__) . 'includes/class-resume-form.php';
    }

    /**
     * Logout Function
     */
    public function custom_logout_script()
    {
        // Enqueue the custom logout script
        wp_enqueue_script('custom-logout', plugin_dir_url(__FILE__) . 'js/custom-logout.js', array('jquery'), null, true);

        // Localize the script with logout data
        wp_localize_script('custom-logout', 'logoutData', array(
            'logoutUrl' => wp_logout_url(home_url()), // Pass the logout URL
        ));
    }

    /**
     * Toolbar Hide Function
     */
    public function hide_admin_toolbar_for_non_admins()
    {
        if (! current_user_can('administrator')) {
            add_filter('show_admin_bar', '__return_false');
        }
    }

    /**
     * Redirect to Dashboard Function
     */
    public function redirect_employer_dashboard()
    {
        if (! is_admin() && is_page('employer-dashboard')) {
            wp_redirect(home_url('/dashboard/'));
            exit();
        }
    }
}

// Initialize the plugin
new AdventistsEmploymentPlugin();
