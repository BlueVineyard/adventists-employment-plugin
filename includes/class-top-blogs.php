<?php

class TopBlogs
{
    public function __construct()
    {
        add_shortcode('top_blogs', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function render_shortcode($atts)
    {
        // Query the latest 4 posts
        $query_args = [
            'posts_per_page' => 4,
            'post_type' => 'post',
            'post_status' => 'publish',
        ];
        $latest_posts = new WP_Query($query_args);

        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/top-blogs-template.php';
        return ob_get_clean();

        return '<p>No posts found.</p>';
    }

    public function enqueue_assets()
    {
        wp_enqueue_style('top-blogs-css', plugin_dir_url(__FILE__) . '../css/top-blogs.css');
        wp_enqueue_script('top-blogs-js', plugin_dir_url(__FILE__) . '../js/top-blogs.js', array('jquery'), null, true);
    }
}

new TopBlogs();
