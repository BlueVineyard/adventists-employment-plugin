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
    // Store shortcodes used on the current page
    private $used_shortcodes = [];

    public function __construct()
    {
        // Hook to initialize shortcodes
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_footer', [$this, 'custom_logout_script']);
        add_action('after_setup_theme', [$this, 'hide_admin_toolbar_for_non_admins']);
        add_action('template_redirect', [$this, 'redirect_employer_dashboard']);

        // Hook to detect shortcodes in content
        add_action('wp', [$this, 'detect_shortcodes']);
    }

    /**
     * Detect shortcodes used in the current page
     */
    public function detect_shortcodes()
    {
        global $post;

        if (is_singular() && is_a($post, 'WP_Post')) {
            // List of all shortcodes to check for
            $shortcodes_to_check = [
                'job_form',
                'blog_filter',
                'related_posts',
                'resume_form',
                'top_blogs',
                'signup_form',
                'shortcode1'
            ];

            // Check each shortcode
            foreach ($shortcodes_to_check as $shortcode) {
                if (has_shortcode($post->post_content, $shortcode)) {
                    $this->used_shortcodes[] = $shortcode;
                }
            }

            // Also check for shortcodes in widgets
            if (is_active_widget(false, false, 'text', true)) {
                $widget_text_instances = get_option('widget_text');

                if (is_array($widget_text_instances)) {
                    foreach ($widget_text_instances as $instance) {
                        if (isset($instance['text'])) {
                            foreach ($shortcodes_to_check as $shortcode) {
                                if (has_shortcode($instance['text'], $shortcode)) {
                                    $this->used_shortcodes[] = $shortcode;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if a shortcode is used on the current page
     */
    public function is_shortcode_used($shortcode)
    {
        return in_array($shortcode, $this->used_shortcodes);
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
