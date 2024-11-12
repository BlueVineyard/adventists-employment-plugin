<?php if ($query->have_posts()): ?>
    <?php while ($query->have_posts()) : $query->the_post(); ?>
        <div class="blog-post">
            <h2><?php the_title(); ?></h2>
            <p><?php the_excerpt(); ?></p>
            <a href="<?php the_permalink(); ?>">Read More</a>
        </div>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>

    <?php $this->display_pagination($query, $page); ?>

<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>