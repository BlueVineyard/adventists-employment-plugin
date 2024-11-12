<div class="ae_related_job_card">
    <!-- This action hook will insert content after the job listing meta -->
    <?php #do_action('single_job_listing_meta_after'); 
    ?>

    <div class="ae_related_job_card-top">
        <img class="ae_related_job_card__img" src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($title); ?>">
        <div>
            <h4 class="ae_related_job_card__title">
                <a href="<?php echo get_the_permalink(); ?>"><?php echo esc_html($title); ?></a>
            </h4>
            <span class="ae_related_job_card__company"><?php echo esc_html($company_name); ?></span>
            <span style="color: #CACACA; font-size: 14px;"> | </span>
        </div>
    </div>

    <?php if ($location): ?>
        <div class="ae_related_job_card__location">
            <?php echo $map_svg; ?> <span><?php echo esc_html($location); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($salary): ?>
        <div class="ae_related_job_card__salary">
            <?php echo $salary_svg; ?> <span><?php echo esc_html($salary); ?> <?php echo $salaryCurrency; ?></span>
        </div>
    <?php endif; ?>

    <hr />

    <div class="ae_related_job_card-bottom">
        <div class="ae_related_job_card__published">
            <?php if ($jobDuration): ?>
                <?php
                $formattedJobDuration = date_i18n('M jS, Y', strtotime($jobDuration));
                echo $time_svg . ' <span>' . esc_html($formattedJobDuration) . '</span>';
                ?>
            <?php endif; ?>
        </div>
        <span class="ae_related_job_card__modified">Updated <?php echo esc_html($last_updated); ?></span>
    </div>
</div>