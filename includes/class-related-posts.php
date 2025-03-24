<?php

class RelatedPosts
{
    public function __construct()
    {
        add_shortcode('related_posts', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function render_shortcode($atts)
    {
        global $post;
        $atts = shortcode_atts([
            'posts_per_page' => 3,
            'post_type' => 'post',
        ], $atts);

        $related_posts = $this->get_related_posts($post->ID, $atts['posts_per_page'], $atts['post_type']);

        if ($related_posts->have_posts()) {
            ob_start();
            while ($related_posts->have_posts()) {
                $related_posts->the_post();
                $this->render_post_template();
            }
            wp_reset_postdata();
            return ob_get_clean();
        } else {
            return '<p>No related posts found.</p>';
        }
    }

    private function get_related_posts($post_id, $posts_per_page, $post_type)
    {
        $categories = wp_get_post_categories($post_id);

        $args = [
            'post_type' => $post_type, // Change this if using a custom post type
            'posts_per_page' => $posts_per_page,
            'post__not_in' => [$post_id],
            'orderby' => 'DESC', // Random order for variety
            'post_status' => 'publish',
        ];

        if ($post_type === 'job_listing') {
            $company_name = get_post_meta($post_id, '_company_name', true);
            if ($company_name) {
                $args['meta_query'] = [
                    [
                        'key' => '_company_name',
                        'value' => $company_name,
                        'compare' => '=',
                    ],
                ];
            }
        } else {
            $categories = wp_get_post_categories($post_id);
            $args['category__in'] = $categories;
        }

        return new WP_Query($args);
    }

    private function render_post_template()
    {
        $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        $title = get_the_title();
        $company_name = get_post_meta(get_the_ID(), '_company_name', true);
        $location = get_post_meta(get_the_ID(), '_job_location', true);
        $salary = get_post_meta(get_the_ID(), '_job_salary', true);
        $salaryCurrency = get_post_meta(get_the_ID(), '_job_salary_currency', true);
        $jobDuration = get_post_meta(get_the_ID(), '_job_duration', true);
        $last_updated = get_the_modified_date('M jS, Y');

        $map_svg = '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.33337 8.95258C3.33337 5.20473 6.31814 2.1665 10 2.1665C13.6819 2.1665 16.6667 5.20473 16.6667 8.95258C16.6667 12.6711 14.5389 17.0102 11.2192 18.5619C10.4453 18.9236 9.55483 18.9236 8.78093 18.5619C5.46114 17.0102 3.33337 12.6711 3.33337 8.95258Z" stroke="#3D3935" stroke-width="1.5" /><ellipse cx="10" cy="8.8335" rx="2.5" ry="2.5" stroke="#3D3935" stroke-width="1.5" /></svg>';
        $salary_svg = '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10.4998" r="8.33333" stroke="#3D3935" stroke-width="1.5"/><path d="M10 5.5V15.5" stroke="#3D3935" stroke-width="1.5" stroke-linecap="round"/><path d="M12.5 8.41683C12.5 7.26624 11.3807 6.3335 10 6.3335C8.61929 6.3335 7.5 7.26624 7.5 8.41683C7.5 9.56742 8.61929 10.5002 10 10.5002C11.3807 10.5002 12.5 11.4329 12.5 12.5835C12.5 13.7341 11.3807 14.6668 10 14.6668C8.61929 14.6668 7.5 13.7341 7.5 12.5835" stroke="#3D3935" stroke-width="1.5" stroke-linecap="round"/></svg>';
        $time_svg = '<svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.66659 10.5003C1.66659 15.1027 5.39755 18.8337 9.99992 18.8337C14.6023 18.8337 18.3333 15.1027 18.3333 10.5003C18.3333 5.89795 14.6023 2.16699 9.99992 2.16699" stroke="#D83636" stroke-width="1.5" stroke-linecap="round"/><path d="M10 8V11.3333H13.3333" stroke="#D83636" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="10.5003" r="8.33333" stroke="#D83636" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="0.5 3.5"/></svg>';

        include plugin_dir_path(__FILE__) . '../templates/related-posts-template.php';
    }

    public function enqueue_assets()
    {
        wp_enqueue_style('related-posts-css', plugin_dir_url(__FILE__) . '../css/related-posts.css');
    }
}

new RelatedPosts();
