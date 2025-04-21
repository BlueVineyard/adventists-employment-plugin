<?php

class BlogFilter
{
    public function __construct()
    {
        add_shortcode('blog_filter', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_nopriv_filter_blogs', [$this, 'filter_blogs']);
        add_action('wp_ajax_filter_blogs', [$this, 'filter_blogs']);
    }


    /**
     * 
     * Function - Render Shortcode
     *
     */
    public function render_shortcode($atts)
    {
        ob_start();
        // Display the checkboxes for categories
        $categories = get_categories();
?>
        <div id="blog-filter-scrollTop"></div>
        <div id="blog-filter">
            <div class="blog-filter-categories">
                <button class="blog-category-button active" data-category="all">All</button>
                <?php foreach ($categories as $category): ?>
                    <button class="blog-category-button" data-category="<?php echo esc_attr($category->term_id); ?>">
                        <?php echo esc_html($category->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div id="blog-filter-results">
                <?php $this->display_blogs(); ?>
            </div>
            <div id="blog-pagination"></div>
        </div>
        <?php
        return ob_get_clean();
    }


    /**
     * 
     * Function - Display Blogs
     *
     */
    public function display_blogs($category_ids = [], $page = 1)
    {
        // Define the query arguments
        $args = [
            'post_type' => 'post',
            'posts_per_page' => 6,
            'paged' => $page,
            'category__in' => $category_ids,
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post(); ?>
                <div class="blog-post">
                    <div class="blog-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('full'); ?>
                        </a>
                    </div>
                    <div class="blog-content">
                        <span class="blog-date"><?php echo get_the_date(); ?></span>
                        <h3 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="blog-excerpt"><?php echo wp_trim_words(get_the_content(), 5, '...'); ?></p>
                        <a class="read-more" href="<?php the_permalink(); ?>">Read More</a>
                    </div>
                </div>
<?php endwhile;
            wp_reset_postdata();
            // Display AJAX-compatible pagination
            $this->display_pagination($query, $page);
        else :
            echo '<p>No posts found.</p>';
        endif;
    }


    /**
     * 
     * Function - Display Pagination
     *
     */
    public function display_pagination($query, $current_page)
    {
        $total_pages = $query->max_num_pages;
        $range = 2; // Number of page links to show around the current page

        // if ($total_pages > 1) {
        //     echo '<div class="pagination">';
        //     for ($i = 1; $i <= $total_pages; $i++) {
        //         if ($i == $current_page) {
        //             echo '<span class="current">' . $i . '</span>';
        //         } else {
        //             echo '<a href="#" data-page="' . $i . '">' . $i . '</a>';
        //         }
        //     }
        //     echo '</div>';
        // }

        if ($total_pages > 1) {
            echo '<div class="pagination">';

            // Previous page link
            if ($current_page > 1) {
                echo '<a href="#" class="prev-page" data-page="' . ($current_page - 1) . '"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.25 19.5L15.75 12L8.25 4.5" stroke="#FF8200" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>';
            }

            // First page link
            if ($current_page > $range + 1) {
                echo '<a href="#" data-page="1">1</a>';
                if ($current_page > $range + 2) {
                    echo '<span>...</span>';
                }
            }

            // Page number links
            for ($i = max(1, $current_page - $range); $i <= min($total_pages, $current_page + $range); $i++) {
                if ($i == $current_page) {
                    echo '<span class="current">' . $i . '</span>';
                } else {
                    echo '<a href="#" data-page="' . $i . '">' . $i . '</a>';
                }
            }

            // Last page link
            if ($current_page < $total_pages - $range) {
                if ($current_page < $total_pages - $range - 1) {
                    echo '<span>...</span>';
                }
                echo '<a href="#" data-page="' . $total_pages . '">' . $total_pages . '</a>';
            }

            // Next page link
            if ($current_page < $total_pages) {
                echo '<a href="#" class="next-page" data-page="' . ($current_page + 1) . '"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.25 19.5L15.75 12L8.25 4.5" stroke="#FF8200" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>';
            }

            echo '</div>';
        }
    }

    /**
     * 
     * Function - Enqueue Assets
     *
     */
    public function enqueue_assets()
    {
        // Get the main plugin instance
        global $adventists_employment_plugin;

        // Only enqueue assets if the shortcode is used on this page
        if (
            !isset($adventists_employment_plugin) || !method_exists($adventists_employment_plugin, 'is_shortcode_used') ||
            $adventists_employment_plugin->is_shortcode_used('blog_filter')
        ) {

            wp_enqueue_style('blog-filter-css', plugin_dir_url(__FILE__) . '../css/blog-filter.css');
            wp_enqueue_script('blog-filter-js', plugin_dir_url(__FILE__) . '../js/blog-filter.js', ['jquery'], null, true);

            // Pass AJAX URL to JavaScript
            wp_localize_script('blog-filter-js', 'blogFilterParams', [
                'ajax_url' => admin_url('admin-ajax.php'),
            ]);
        }
    }

    /**
     * 
     * Function - Filter Blogs
     *
     */
    public function filter_blogs()
    {
        $category_id = isset($_POST['categories']) ? $_POST['categories'] : 'all';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

        // If "All" is selected or no specific category is selected, show all posts
        if ($category_id === 'all' || empty($category_id)) {
            $category_ids = []; // Reset to empty to show all posts
        } else {
            $category_ids = is_array($category_id) ? array_map('intval', $category_id) : [intval($category_id)];
        }

        $this->display_blogs($category_ids, $page);
        wp_die();
    }
}

new BlogFilter();
