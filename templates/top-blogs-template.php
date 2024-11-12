<?php if ($latest_posts->have_posts()): ?>
    <div id="top-blogs">
        <div class="top-blogs-container">
            <?php
            $count = 0;
            while ($latest_posts->have_posts()) : $latest_posts->the_post();
                $count++;

                if ($count == 1) : ?>
                    <!-- Left Column -->
                    <div class="top-blog-left">
                        <div class="blog-post">
                            <div class="blog-image">
                                <?php the_post_thumbnail('full'); ?>
                            </div>
                            <div class="blog-content">
                                <span class="blog-date"><?php echo get_the_date(); ?></span>
                                <h2 class="blog-title"><?php the_title(); ?></h2>
                                <p class="blog-excerpt"><?php echo wp_trim_words(get_the_content(), 20, '...'); ?></p>
                                <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                            </div>
                        </div>
                    </div><!-- .top-blog-left -->

                    <!-- Open the right column container for the next posts -->
                    <div class="top-blog-right">
                    <?php else: ?>
                        <!-- Right Column -->
                        <div class="blog-post">
                            <div class="blog-image">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </div>
                            <div class="blog-content">
                                <span class="blog-date"><?php echo get_the_date(); ?></span>
                                <h3 class="blog-title"><?php the_title(); ?></h3>
                                <p class="blog-excerpt"><?php echo wp_trim_words(get_the_content(), 5, '...'); ?></p>
                                <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                            </div>
                        </div>
                        <hr>
                    <?php endif; ?>
                <?php endwhile; ?>
                <!-- Close the right column container -->
                    </div> <!-- .top-blog-right -->
        </div> <!-- .top-blogs-container -->
    </div> <!-- #top-blogs -->
    <?php wp_reset_postdata(); ?>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>