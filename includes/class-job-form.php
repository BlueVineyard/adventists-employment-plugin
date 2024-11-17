<?php
class JobListingForm
{

    public function __construct()
    {
        add_shortcode('job_form', [$this, 'render_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        // add_action('init', [$this, 'handle_form_submission']);

        // Hooks for form handling in admin-post.php
        add_action('admin_post_nopriv_submit_job_listing', [$this, 'handle_form_submission']);
        add_action('admin_post_submit_job_listing', [$this, 'handle_form_submission']);

        // Hooks for AJAX form handling
        add_action('wp_ajax_nopriv_submit_job_listing_ajax', [$this, 'handle_form_submission_ajax']);
        add_action('wp_ajax_submit_job_listing_ajax', [$this, 'handle_form_submission_ajax']);

        // Add AJAX action to fetch Employers
        add_action('wp_ajax_fetch_employer_details', [$this, 'fetch_employer_details']);
        add_action('wp_ajax_nopriv_fetch_employer_details', [$this, 'fetch_employer_details']);
    }

    public function render_form($atts)
    {
        ob_start();

        // Display feedback message
        if (isset($_GET['job_message'])) {
            echo '<div class="notice notice-success"><p>' . esc_html($_GET['job_message']) . '</p></div>';
        }

        // Check if we're editing an existing job
        $job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

        // $this->generate_form($atts);
        $this->generate_form($job_id);
        return ob_get_clean();
    }

    private function generate_form($job_id)
    {
        // If editing, retrieve the meta field values, otherwise initialize as empty
        $job_title = $job_id ? get_post_meta($job_id, '_job_title', true) : '';
        $job_salary = $job_id ? get_post_meta($job_id, '_job_salary', true) : '';
        $job_description = $job_id ? get_post_meta($job_id, '_job_description', true) : '';
        $what_do_we_offer = $job_id ? get_post_meta($job_id, '_what_do_we_offer', true) : '';
        $who_are_we = $job_id ? get_post_meta($job_id, '_who_are_we', true) : '';
        $key_responsibilities = $job_id ? get_post_meta($job_id, '_key_responsibilities', true) : '';
        $how_to_apply = $job_id ? get_post_meta($job_id, '_how_to_apply', true) : '';
        $company_logo = $job_id ? get_post_meta($job_id, '_company_logo', true) : '';
        $company_name = $job_id ? get_post_meta($job_id, '_company_name', true) : '';
        $company_website = $job_id ? get_post_meta($job_id, '_company_website', true) : '';
        $application_period = $job_id ? get_post_meta($job_id, '_application_deadline', true) : '';
        $apply_externally = $job_id ? get_post_meta($job_id, 'apply_externally', true) : '';
        $external_application_link = $job_id ? get_post_meta($job_id, 'external_application_link', true) : '';


        // Get the post status to determine if the job is published
        $job_status = $job_id ? get_post_status($job_id) : 'draft';

        // Retrieve current selected job type
        $selected_job_type = wp_get_post_terms($job_id, 'job_listing_type', ['fields' => 'ids']);
        $selected_job_type = !empty($selected_job_type) ? $selected_job_type[0] : '';  // Select the first term if available

        // Retrieve current selected job category
        $selected_job_category = wp_get_post_terms($job_id, 'job_listing_category', ['fields' => 'ids']);
        $selected_job_category = !empty($selected_job_category) ? $selected_job_category[0] : '';  // Select the first term if available

        // Retrieve selected job location(s) from the ACF taxonomy field
        $selected_locations =  wp_get_post_terms($job_id, 'location', ['fields' => 'ids']);





        // Output the form
?>

        <div id="add_job">
            <div class="add_job-form">
                <a href="javascript:void(0);" class="back_btn" onclick="history.back();">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 7H15M1 7L7 13M1 7L7 1" stroke="#FF8200" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Back</span>
                </a>

                <h1>Add Job</h1>
                <form method="POST" id="submit-job-form" class="job-manager-form" enctype="multipart/form-data">
                    <input type="hidden" name="_wp_http_referer" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />

                    <input type="hidden" name="action" value="submit_job_listing_ajax" />

                    <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>" />
                    <!-- <input type="hidden" name="action" value="submit_job_listing" /> -->

                    <!-- Nonce Field for Security -->
                    <?php wp_nonce_field('job_listing_nonce_action', 'job_listing_nonce_field'); ?>

                    <div id="jobDetails" class="ae_form_card">
                        <h4 class="ae_form_card-title">Job Details</h4>
                        <?php if ($job_id) : ?>
                            <div class="job-status">
                                Status: <?php echo ucfirst($job_status); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Job Title Field -->
                        <fieldset class="fieldset-job_title fieldset-type-title">
                            <label for="job_title" class="ae_label"><?php esc_html_e('Job Title', 'wp-job-manager'); ?></label>
                            <input type="text" name="job_title" class="ae_input" id="job_title" value="<?php echo esc_attr($job_title); ?>" />
                        </fieldset>

                        <!-- Location Select Field -->
                        <fieldset class="fieldset-location fieldset-type-select half_field">
                            <label for="job_location" class="ae_label"><?php esc_html_e('Location', 'wp-job-manager'); ?></label>
                            <select name="job_location" id="job_location" class="ae_input">
                                <option value=""><?php esc_html_e('Select Location', 'wp-job-manager'); ?></option>
                                <?php
                                $locations = get_terms(['taxonomy' => 'location', 'hide_empty' => false]);
                                foreach ($locations as $location) {
                                ?>
                                    <option value="<?php echo esc_attr($location->term_id); ?>" <?php selected(in_array($location->term_id, $selected_locations)); ?>>
                                        <?php echo esc_html($location->name); ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </fieldset>


                        <!-- Job Salary Field -->
                        <fieldset class="fieldset-job_salary fieldset-type-salary half_field">
                            <label for="job_salary" class="ae_label"><?php esc_html_e('Job Salary', 'wp-job-manager'); ?></label>
                            <input type="text" name="job_salary" class="ae_input" id="job_salary" value="<?php echo esc_attr($job_salary); ?>" />
                        </fieldset>

                        <!-- Job Type Select Field -->
                        <fieldset class="fieldset-job_type fieldset-type-select half_field">
                            <label for="job_type" class="ae_label"><?php esc_html_e('Type of Work', 'wp-job-manager'); ?></label>
                            <select name="job_type" id="job_type" class="ae_input">
                                <option value=""><?php esc_html_e('Select Job Type', 'wp-job-manager'); ?></option>
                                <?php
                                $job_types = get_terms(['taxonomy' => 'job_listing_type', 'hide_empty' => false]);
                                foreach ($job_types as $job_type) {
                                ?>
                                    <option value="<?php echo esc_attr($job_type->term_id); ?>" <?php selected($selected_job_type, $job_type->term_id); ?>>
                                        <?php echo esc_html($job_type->name); ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </fieldset>

                        <!-- Job Category Select Field -->
                        <fieldset class="fieldset-job_category fieldset-type-select half_field">
                            <label for="job_category" class="ae_label"><?php esc_html_e('Category of Work', 'wp-job-manager'); ?></label>
                            <select name="job_category" id="job_category" class="ae_input">
                                <option value=""><?php esc_html_e('Select Job Category', 'wp-job-manager'); ?></option>
                                <?php
                                $job_categories = get_terms(['taxonomy' => 'job_listing_category', 'hide_empty' => false]);
                                foreach ($job_categories as $job_category) {
                                ?>
                                    <option value="<?php echo esc_attr($job_category->term_id); ?>" <?php selected($selected_job_category, $job_category->term_id); ?>>
                                        <?php echo esc_html($job_category->name); ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </fieldset>

                        <!-- Application Period Field -->
                        <fieldset class="fieldset-application_period fieldset-type-date half_field">
                            <label for="application_period" class="ae_label"><?php esc_html_e('Application Period', 'wp-job-manager'); ?></label>
                            <input type="text" class="input-date job-manager-datepicker ae_input" name="application_period" id="application_period" placeholder="<?php esc_attr_e('Select application period', 'wp-job-manager'); ?>" value="<?php echo esc_attr($application_period); ?>" />
                        </fieldset>

                        <!-- Apply Externally (Radio) Field -->
                        <fieldset class="fieldset-apply_externally fieldset-type-radio half_field">
                            <label for="apply_externally" class="ae_label"><?php esc_html_e('Apply Externally', 'wp-job-manager'); ?></label>
                            <div>
                                <input type="radio" name="apply_externally" value="yes" <?php checked($apply_externally, 'yes'); ?> /> <span>Yes</span>
                                &nbsp;&nbsp;
                                <input type="radio" name="apply_externally" value="no" <?php checked($apply_externally, 'no'); ?> /> <span>No</span>
                            </div>
                        </fieldset>

                        <!-- External Application Link Field (Initially Hidden) -->
                        <fieldset class="fieldset-external_application_link fieldset-type-url half_field" id="external_application_link_field" style="display: none;">
                            <label for="external_application_link" class="ae_label"><?php esc_html_e('External Application Link', 'wp-job-manager'); ?></label>
                            <input type="url" name="external_application_link" id="external_application_link" class="ae_input" value="<?php echo esc_url($external_application_link); ?>" />
                        </fieldset>


                        <!-- Job Description Field (WYSIWYG Editor) -->
                        <fieldset class="fieldset-job_description fieldset-type-description">
                            <label for="job_description" class="ae_label" style="margin-bottom: -30px;"><?php esc_html_e('About The Role', 'wp-job-manager'); ?></label>
                            <?php
                            wp_editor(
                                $job_description,          // The content to display in the editor.
                                'job_description',         // The ID of the textarea element.
                                array(
                                    'textarea_name' => 'job_description', // The name attribute of the textarea.
                                    'textarea_rows' => 14,                 // Number of rows in the editor.
                                    'media_buttons' => false,             // Hide "Add Media" button.
                                    'tinymce'       => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink', // Toolbar customization.
                                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',                  // Hide second toolbar row.
                                        'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;', // Formats.
                                    ),
                                )
                            );
                            ?>
                        </fieldset>
                    </div>

                    <div class="spacer-20"></div>

                    <div id="whoAreWe" class="ae_form_card">
                        <h4 class="ae_form_card-title">Who Are We?</h4>
                        <!-- Who We Are Field (WYSIWYG Editor) -->
                        <fieldset class="fieldset-who_are_we fieldset-type-description">
                            <label for="who_are_we" class="ae_label" style="margin-bottom: -30px;"><?php esc_html_e('Description', 'wp-job-manager'); ?></label>
                            <?php
                            wp_editor(
                                $who_are_we,          // The content to display in the editor.
                                'who_are_we',         // The ID of the textarea element.
                                array(
                                    'textarea_name' => 'who_are_we', // The name attribute of the textarea.
                                    'textarea_rows' => 14,                 // Number of rows in the editor.
                                    'media_buttons' => false,             // Hide "Add Media" button.
                                    'tinymce'       => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink', // Add 'formatselect' for paragraphs/headings.
                                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',                  // Hide second toolbar row.
                                        'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;', // Specify available formats.
                                    ),
                                )
                            );
                            ?>
                        </fieldset>
                    </div>
                    <div class="spacer-20"></div>

                    <div id="whatDoWeOffer" class="ae_form_card">
                        <h4 class="ae_form_card-title">What Do We Offer?</h4>
                        <!-- Who We Are Field (WYSIWYG Editor) -->
                        <fieldset class="fieldset-what_do_we_offer fieldset-type-description">
                            <label for="what_do_we_offer" class="ae_label" style="margin-bottom: -30px;"><?php esc_html_e('Offer', 'wp-job-manager'); ?></label>
                            <?php
                            wp_editor(
                                $what_do_we_offer,          // The content to display in the editor.
                                'what_do_we_offer',         // The ID of the textarea element.
                                array(
                                    'textarea_name' => 'what_do_we_offer', // The name attribute of the textarea.
                                    'textarea_rows' => 14,                 // Number of rows in the editor.
                                    'media_buttons' => false,             // Hide "Add Media" button.
                                    'tinymce'       => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink', // Add 'formatselect' for paragraphs/headings.
                                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',                  // Hide second toolbar row.
                                        'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;', // Specify available formats.
                                    ),
                                )
                            );
                            ?>
                        </fieldset>
                    </div>
                    <div class="spacer-20"></div>

                    <div id="responsibilities" class="ae_form_card">
                        <h4 class="ae_form_card-title">Key Responsibilities</h4>
                        <!-- Who We Are Field (WYSIWYG Editor) -->
                        <fieldset class="fieldset-key_responsibilities fieldset-type-description">
                            <label for="key_responsibilities" class="ae_label" style="margin-bottom: -30px;"><?php esc_html_e('Responsibilities', 'wp-job-manager'); ?></label>
                            <?php
                            wp_editor(
                                $key_responsibilities,          // The content to display in the editor.
                                'key_responsibilities',         // The ID of the textarea element.
                                array(
                                    'textarea_name' => 'key_responsibilities', // The name attribute of the textarea.
                                    'textarea_rows' => 14,                 // Number of rows in the editor.
                                    'media_buttons' => false,             // Hide "Add Media" button.
                                    'tinymce'       => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink', // Add 'formatselect' for paragraphs/headings.
                                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',                  // Hide second toolbar row.
                                        'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;', // Specify available formats.
                                    ),
                                )
                            );
                            ?>
                        </fieldset>
                    </div>
                    <div class="spacer-20"></div>

                    <div id="howToApply" class="ae_form_card">
                        <h4 class="ae_form_card-title">How To Apply?</h4>
                        <!-- Who We Are Field (WYSIWYG Editor) -->
                        <fieldset class="fieldset-how_to_apply fieldset-type-description">
                            <label for="how_to_apply" class="ae_label" style="margin-bottom: -30px;"><?php esc_html_e('Description', 'wp-job-manager'); ?></label>
                            <?php
                            wp_editor(
                                $how_to_apply,          // The content to display in the editor.
                                'how_to_apply',         // The ID of the textarea element.
                                array(
                                    'textarea_name' => 'how_to_apply', // The name attribute of the textarea.
                                    'textarea_rows' => 14,                 // Number of rows in the editor.
                                    'media_buttons' => false,             // Hide "Add Media" button.
                                    'tinymce'       => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink', // Add 'formatselect' for paragraphs/headings.
                                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',                  // Hide second toolbar row.
                                        'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;', // Specify available formats.
                                    ),
                                )
                            );
                            ?>
                        </fieldset>
                    </div>

                    <div class="spacer-20"></div>

                    <div id="companyDetails" class="ae_form_card">
                        <h4 class="ae_form_card-title">Company Details</h4>

                        <!-- Employer Dropdown -->
                        <fieldset class="fieldset-employer">
                            <label for="employer" class="ae_label"><?php esc_html_e('Select Employer', 'wp-job-manager'); ?></label>
                            <select name="employer" id="employer" class="ae_input">
                                <option value=""><?php esc_html_e('Select an Employer', 'wp-job-manager'); ?></option>
                                <?php
                                $employers = get_posts([
                                    'post_type'   => 'employer-dashboard',
                                    'post_status' => 'publish',
                                    'numberposts' => -1,
                                ]);
                                foreach ($employers as $employer) {
                                    $employer_name = get_field('company_name', $employer->ID);
                                    echo '<option value="' . esc_attr($employer->ID) . '">' . esc_html($employer_name) . '</option>';
                                }
                                ?>
                            </select>
                        </fieldset>

                        <!-- Company Logo Upload Field -->
                        <fieldset class="fieldset-company_logo">
                            <label class="ae_label"><?php esc_html_e('Employer Logo', 'wp-job-manager'); ?></label>
                            <div id="company_logo_preview">
                                <?php
                                if (has_post_thumbnail($job_id)) {
                                    echo get_the_post_thumbnail($job_id, 'full');
                                    echo '<input type="hidden" name="company_logo_current" value="' . get_post_thumbnail_id($job_id) . '" />';
                                }
                                ?>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset-company_name half_field">
                            <label class="ae_label"><?php esc_html_e('Employer Name', 'wp-job-manager'); ?></label>
                            <input type="text" name="company_name" id="company_name" class="ae_input" value="<?php echo esc_attr($company_name); ?>" readonly />
                        </fieldset>
                        <fieldset class="fieldset-company_website half_field">
                            <label class="ae_label"><?php esc_html_e('Employer Website', 'wp-job-manager'); ?></label>
                            <input type="url" name="company_website" id="company_website" class="ae_input" value="<?php echo esc_url($company_website); ?>" readonly />
                        </fieldset>
                    </div>


                    <!-- Save and Cancel Buttons -->
                    <div class="add_job-form-btns">
                        <a href="javascript:void(0);" onclick="history.back();" class="cancel-btn"><?php esc_attr_e('Cancel', 'wp-job-manager'); ?></a>
                        <?php if ($job_status === 'draft' || !$job_id) : ?>
                            <input type="submit" name="job_submit" class="button button-primary" value="<?php esc_attr_e('Publish', 'wp-job-manager'); ?>" />
                            <!-- Only show the "Save Draft" button for new or draft jobs -->
                            <input type="submit" name="save_draft" class="button button-secondary" value="<?php esc_attr_e('Save Draft', 'wp-job-manager'); ?>" />
                        <?php else: ?>
                            <input type="submit" name="job_submit" class="button button-primary" value="<?php esc_attr_e('Save', 'wp-job-manager'); ?>" />
                        <?php endif; ?>

                    </div>
                </form>
            </div>
            <div class="add_job-nav">
                <a href="#" class="add_job-nav-link add_job-jobDetails">Job Details</a>
                <a href="#" class="add_job-nav-link add_job-whoAreWe">Who are we?</a>
                <a href="#" class="add_job-nav-link add_job-whatDoWeOffer">What do we Offer?</a>
                <a href="#" class="add_job-nav-link add_job-responsibilities">Key Responsibilities</a>
                <a href="#" class="add_job-nav-link add_job-howToApply">How to Apply</a>
                <a href="#" class="add_job-nav-link add_job-companyDetails">Company Details</a>
            </div>
        </div>
<?php
    }


    public function handle_form_submission_ajax()
    {
        // Verify the nonce
        check_ajax_referer('job_listing_nonce_action', 'security');

        // Validate and sanitize form data
        $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
        $job_title = sanitize_text_field($_POST['job_title']);
        $job_description = wp_kses_post($_POST['job_description']);
        $job_salary = wp_kses_post($_POST['job_salary']);

        $what_do_we_offer = wp_kses_post($_POST['what_do_we_offer']);;
        $who_are_we = wp_kses_post($_POST['who_are_we']);;
        $key_responsibilities = wp_kses_post($_POST['key_responsibilities']);;
        $how_to_apply = wp_kses_post($_POST['how_to_apply']);;

        $company_name = sanitize_text_field($_POST['company_name']);
        $company_website = esc_url_raw($_POST['company_website']);
        $application_period = sanitize_text_field($_POST['application_period']);

        $apply_externally = isset($_POST['apply_externally']) ? sanitize_text_field($_POST['apply_externally']) : '';
        $external_application_link = isset($_POST['external_application_link']) ? esc_url_raw($_POST['external_application_link']) : '';

        $job_type = isset($_POST['job_type']) ? intval($_POST['job_type']) : 0;
        $job_category = isset($_POST['job_category']) ? intval($_POST['job_category']) : 0;
        $job_location = isset($_POST['job_location']) ? intval($_POST['job_location']) : 0;

        // Determine if we are saving as a draft or publishing
        $post_status = 'publish'; // Default to draft

        if (isset($_POST['job_submit'])) {
            $post_status = 'publish'; // Set to publish if "Publish" button is clicked
        } elseif (isset($_POST['save_draft'])) {
            $post_status = 'draft'; // Explicitly set to draft if "Save Draft" button is clicked
        }


        // Prepare post data
        $post_data = [
            'post_title'   => $job_title,
            'post_content' => $job_description,
            'post_status'  => $post_status,
            'post_type'    => 'job_listing',
        ];

        // Insert or update job listing
        if ($job_id) {
            $post_data['ID'] = $job_id;
            $result = wp_update_post($post_data);
        } else {
            $result = wp_insert_post($post_data);
        }

        // Save company logo (based on employer selection)
        if (isset($_POST['employer'])) {
            $employer_id = intval($_POST['employer']);
            $company_logo_id = get_field('company_logo', $employer_id); // Get the logo attachment ID from the employer CPT
            if ($company_logo_id) {
                update_post_meta($result, '_company_logo', $company_logo_id); // Save the logo ID as meta
                set_post_thumbnail($result, $company_logo_id); // Set as featured image
            }
        }


        if (is_wp_error($result)) {
            wp_send_json_error(['message' => 'Error: ' . $result->get_error_message()]);
        } else {
            // Update meta fields and job type
            update_post_meta($result, '_job_title', $job_title);
            update_post_meta($result, '_job_description', $job_description);
            update_post_meta($result, '_job_salary', $job_salary);

            update_post_meta($result, '_what_do_we_offer', $what_do_we_offer);
            update_post_meta($result, '_who_are_we', $who_are_we);
            update_post_meta($result, '_key_responsibilities', $key_responsibilities);
            update_post_meta($result, '_how_to_apply', $how_to_apply);

            update_post_meta($result, '_company_name', $company_name);
            update_post_meta($result, '_company_website', $company_website);
            update_post_meta($result, '_application_deadline', $application_period);
            update_post_meta($result, '_job_expires', $application_period);

            update_post_meta($result, 'apply_externally', $apply_externally);
            update_post_meta($result, 'external_application_link', $external_application_link);

            if ($job_type) {
                wp_set_post_terms($result, [$job_type], 'job_listing_type');
            }
            if ($job_category) {
                wp_set_post_terms($result, [$job_category], 'job_listing_category');
            }
            if ($job_location) {
                wp_set_post_terms($result, [$job_location], 'location');
                // Retrieve the location term name
                $location_term = get_term($job_location);
                $location_name = $location_term ? $location_term->name : '';

                // Update the post meta with the location name
                update_post_meta($result, '_job_location', $location_name);
            }

            // Redirect to the same page, including the job ID in the URL
            $redirect_url = add_query_arg('job_id', $result, esc_url_raw($_POST['_wp_http_referer']));
            $status_message = $post_status === 'draft' ? 'Job saved as draft!' : 'Job published successfully!';
            wp_send_json_success(['message' => $status_message, 'redirect_url' => $redirect_url]);
        }

        wp_die(); // Always end AJAX functions with wp_die()
    }





    public function enqueue_assets()
    {
        wp_enqueue_script('wp-job-manager-term-multiselect');
        wp_enqueue_script('wp-job-manager-datepicker');
        wp_enqueue_style('jquery-ui');

        wp_enqueue_style('job-form-css', plugin_dir_url(__FILE__) . '../css/job-form.css');
        wp_enqueue_script('job-form-js', plugin_dir_url(__FILE__) . '../js/job-form.js', array('jquery'), null, true);

        // Localize script to pass AJAX URL and nonce
        wp_localize_script('job-form-js', 'jobFormAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('job_listing_nonce_action'),
        ));
    }

    public function fetch_employer_details()
    {
        // Check the nonce
        check_ajax_referer('job_listing_nonce_action', 'security');

        $employer_id = intval($_POST['employer_id']);
        if (!$employer_id) {
            wp_send_json_error(['message' => 'Invalid Employer ID']);
        }

        // Retrieve ACF fields
        $company_logo_id = get_field('company_logo', $employer_id); // This returns the attachment ID
        $company_logo_url = $company_logo_id ? wp_get_attachment_image_url($company_logo_id, 'full') : '';
        $company_name = get_field('company_name', $employer_id);
        $company_website = get_field('website', $employer_id);

        $response = [
            'company_logo_id' => $company_logo_id,
            'company_logo' => $company_logo_url,
            'company_name' => $company_name,
            'company_website' => $company_website,
        ];

        wp_send_json_success($response);
    }
}

new JobListingForm();
