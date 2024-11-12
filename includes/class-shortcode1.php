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
        // if (has_shortcode(get_post()->post_content, 'shortcode1')) {
        wp_enqueue_style('shortcode1-css', plugin_dir_url(__FILE__) . '../css/shortcode1.css');
        wp_enqueue_script('shortcode1-js', plugin_dir_url(__FILE__) . '../js/shortcode1.js', array('jquery'), null, true);
        // }
    }
}

new Shortcode1();