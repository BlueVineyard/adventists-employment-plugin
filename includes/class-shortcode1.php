<?php

class Shortcode1
{
    public function __construct()
    {
        add_shortcode('shortcode1', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function render_shortcode($atts)
    {
        // Handle attributes and rendering here
        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/shortcode1-template.php';
        return ob_get_clean();
    }

    public function enqueue_assets()
    {
        // Get the main plugin instance
        global $adventists_employment_plugin;

        // Only enqueue assets if the shortcode is used on this page
        if (
            !isset($adventists_employment_plugin) || !method_exists($adventists_employment_plugin, 'is_shortcode_used') ||
            $adventists_employment_plugin->is_shortcode_used('shortcode1')
        ) {

            wp_enqueue_style('shortcode1-css', plugin_dir_url(__FILE__) . '../css/shortcode1.css');
            wp_enqueue_script('shortcode1-js', plugin_dir_url(__FILE__) . '../js/shortcode1.js', array('jquery'), null, true);
        }
    }
}

new Shortcode1();
