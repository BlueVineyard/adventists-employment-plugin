<?php
class ResumeForm
{
    public function __construct()
    {
        // Register shortcode for resume form
        add_shortcode('resume_form', [$this, 'render_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Hooks for form handling via admin-post.php
        add_action('admin_post_nopriv_submit_resume', [$this, 'handle_form_submission']);
        add_action('admin_post_submit_resume', [$this, 'handle_form_submission']);

        // Hooks for AJAX form handling
        add_action('wp_ajax_nopriv_submit_resume_ajax', [$this, 'handle_form_submission_ajax']);
        add_action('wp_ajax_submit_resume_ajax', [$this, 'handle_form_submission_ajax']);

        //Hooks for AJAX Skill Autocomplete
        add_action('wp_ajax_fetch_skills', [$this, 'fetch_skills']);
        add_action('wp_ajax_nopriv_fetch_skills', [$this, 'fetch_skills']);
    }

    // Render the form via shortcode
    public function render_form($atts)
    {
        ob_start();

        // Display feedback message
        if (isset($_GET['resume_message'])) {
            echo '<div class="notice notice-success"><p>' . esc_html($_GET['resume_message']) . '</p></div>';
        }

        // Check if we're editing an existing resume
        $resume_id = isset($_GET['resume_id']) ? intval($_GET['resume_id']) : 0;

        // Render the form
        $this->generate_form($resume_id);
        return ob_get_clean();
    }

    private function generate_form($resume_id)
    {
        // If editing, retrieve the meta field values, otherwise initialize as empty
        $candidate_photo = $resume_id ? get_post_meta($resume_id, '_candidate_photo', true) : '';
        $resume_title = $resume_id ? get_the_title($resume_id) : '';

        $professional_title = $resume_id ? get_post_meta($resume_id, '_candidate_title', true) : '';
        $right_to_work_value  = $resume_id ? get_post_meta($resume_id, '_candidate_right_to_work', true) : '';

        $right_to_work_options = array(
            '' => __('Select an Option', 'job_manager'),
            'australian-citizen' => __('Australian Citizen', 'job_manager'),
            'foreign-citizen' => __('Foreign Citizen', 'job_manager'),
        );


        $resume_description = $resume_id ? get_post_field('post_content', $resume_id) : '';
        $resume_file = $resume_id ? get_post_meta($resume_id, '_resume_file', true) : '';

        // Get post status
        $resume_status = $resume_id ? get_post_status($resume_id) : 'draft';

        // Get the candidate experience meta data
        $candidate_experience = $resume_id ? get_post_meta($resume_id, '_candidate_experience', true) : [];
        // Get the candidate education meta data
        $candidate_education = $resume_id ? get_post_meta($resume_id, '_candidate_education', true) : [];
        // Get the candidate certifications meta data
        $resume_certifications = $resume_id ? get_post_meta($resume_id, '_resume_certifications', true) : [];

        // Get selected skills (taxonomy terms) for the resume
        $selected_skills = $resume_id ? wp_get_object_terms($resume_id, 'resume_skill', array('fields' => 'names')) : [];
        $selected_skills = !is_wp_error($selected_skills) ? $selected_skills : [];

?>

        <div id="add_resume">
            <div class="add_resume-form">
                <a href="javascript:void(0);" class="back_btn" onclick="history.back();">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 7H15M1 7L7 13M1 7L7 1" stroke="#FF8200" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span>Back</span>
                </a>

                <h1>Add Resume</h1>
                <form method="POST" id="submit-resume-form" class="resume-manager-form" enctype="multipart/form-data">
                    <input type="hidden" name="_wp_http_referer" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />
                    <input type="hidden" name="action" value="submit_resume_ajax" />
                    <input type="hidden" name="resume_id" value="<?php echo esc_attr($resume_id); ?>" />

                    <!-- Nonce Field for Security -->
                    <?php wp_nonce_field('resume_nonce_action', 'resume_nonce_field'); ?>

                    <div id="employeeDetails" class="ae_form_card">
                        <h4 class="ae_form_card-title">Employee Details</h4>
                        <!-- Candidate Photo Field -->
                        <fieldset class="fieldset-candidate_photo fieldset-type-file">
                            <label for="candidate_photo"
                                class="ae_label"><?php esc_html_e('Upload Photo (JPG, PNG)', 'wp-resume-manager'); ?></label>
                            <input type="file" name="candidate_photo" id="candidate_photo" accept="image/jpeg,image/png" />
                            <?php if ($candidate_photo): ?>
                                <input type="hidden" name="candidate_photo_current"
                                    value="<?php echo esc_attr($candidate_photo); ?>" />
                                <p><?php esc_html_e('Current Photo:', 'wp-resume-manager'); ?> <a
                                        href="<?php echo esc_url($candidate_photo); ?>"
                                        target="_blank"><?php esc_html_e('View Photo', 'wp-resume-manager'); ?></a></p>
                            <?php endif; ?>
                        </fieldset>


                        <!-- Resume Title Field -->
                        <fieldset class="fieldset-resume_title fieldset-type-title">
                            <label for="resume_title"
                                class="ae_label"><?php esc_html_e('Employee Name', 'wp-resume-manager'); ?></label>
                            <input type="text" name="resume_title" class="ae_input" id="resume_title"
                                value="<?php echo esc_attr($resume_title); ?>" required />
                        </fieldset>

                        <!-- Professional Title Field -->
                        <fieldset class="fieldset-professional_title fieldset-type-title half_field">
                            <label for="professional_title"
                                class="ae_label"><?php esc_html_e('Role', 'wp-resume-manager'); ?></label>
                            <input type="text" name="professional_title" class="ae_input" id="professional_title"
                                value="<?php echo esc_attr($professional_title); ?>" />
                        </fieldset>

                        <!-- Right to Work Select Field -->
                        <fieldset class="fieldset-right_to_work fieldset-type-select half_field">
                            <label for="right_to_work"
                                class="ae_label"><?php esc_html_e('Right to Work', 'job_manager'); ?></label>
                            <select name="right_to_work" id="right_to_work" class="ae_input">
                                <?php
                                foreach ($right_to_work_options as $key => $label) {
                                ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($right_to_work_value, $key); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </fieldset>

                        <!-- Resume Description Field (WYSIWYG Editor) -->
                        <fieldset class="fieldset-resume_description fieldset-type-description">
                            <label for="resume_description" class="ae_label"
                                style="margin-bottom: -30px;"><?php esc_html_e('Resume Description', 'wp-resume-manager'); ?></label>
                            <?php
                            wp_editor(
                                $resume_description,
                                'resume_description',
                                array(
                                    'textarea_name' => 'resume_description',
                                    'textarea_rows' => 14,
                                    'media_buttons' => false,
                                    'tinymce'       => array(
                                        'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink',
                                        'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
                                        'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',
                                    ),
                                )
                            );
                            ?>
                        </fieldset>

                        <!-- Resume File Upload Field -->
                        <fieldset class="fieldset-resume_file fieldset-type-file">
                            <label for="resume_file"
                                class="ae_label"><?php esc_html_e('Upload Resume (PDF only)', 'wp-resume-manager'); ?></label>
                            <input type="file" name="resume_file" id="resume_file" accept="application/pdf" />
                            <?php if ($resume_file): ?>
                                <p><?php esc_html_e('Current Resume:', 'wp-resume-manager'); ?> <a
                                        href="<?php echo esc_url($resume_file); ?>"><?php echo esc_html(basename($resume_file)); ?></a>
                                </p>
                            <?php endif; ?>
                        </fieldset>
                    </div>

                    <div class="spacer-20"></div>

                    <div id="careerHistory" class="ae_form_card">
                        <h4 class="ae_form_card-title">
                            <?php esc_html_e('Career History', 'wp-resume-manager'); ?>

                            <a href="javascript:void(0);"
                                id="add-experience"><?php esc_html_e('Add Experience', 'wp-resume-manager'); ?></a>
                        </h4>

                        <div id="career-history-repeater">
                            <!-- Hidden template for a blank career history entry -->
                            <div id="career-history-template" style="display: none;">
                                <div class="career-history-entry">
                                    <fieldset class="fieldset-resume_employer fieldset-type-employer">
                                        <label class="ae_label" for="new_employer">Employer</label>
                                        <input class="ae_input" type="text" name="candidate_experience[new][employer]"
                                            id="new_employer" value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_job_title fieldset-type-job-title half_field">
                                        <label class="ae_label" for="new_job_title">Job Title</label>
                                        <input class="ae_input" type="text" name="candidate_experience[new][job_title]"
                                            id="new_job_title" value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_date fieldset-type-date half_field">
                                        <label class="ae_label" for="new_date">Start/End Date</label>
                                        <input class="ae_input" type="text" name="candidate_experience[new][date]" id="new_date"
                                            value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_notes fieldset-type-notes">
                                        <label class="ae_label" for="new_notes">Notes</label>
                                        <textarea class="ae_input" name="candidate_experience[new][notes]"
                                            id="new_notes"></textarea>
                                    </fieldset>

                                    <a href="javascript:void(0);" class="remove-experience">Remove Experience</a>
                                </div>
                            </div>

                            <?php if (!empty($candidate_experience)) : ?>
                                <?php foreach ($candidate_experience as $index => $experience) : ?>
                                    <div class="career-history-entry">
                                        <!-- Employer -->
                                        <fieldset class="fieldset-resume_employer fieldset-type-employer">
                                            <label class="ae_label"
                                                for="employer_<?php echo $index; ?>"><?php esc_html_e('Employer', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="text"
                                                name="candidate_experience[<?php echo $index; ?>][employer]"
                                                id="employer_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($experience['employer']); ?>" required />
                                        </fieldset>

                                        <!-- Job Title -->
                                        <fieldset class="fieldset-resume_job_title fieldset-type-job-title half_field">
                                            <label class="ae_label"
                                                for="job_title_<?php echo $index; ?>"><?php esc_html_e('Job Title', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="text"
                                                name="candidate_experience[<?php echo $index; ?>][job_title]"
                                                id="job_title_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($experience['job_title']); ?>" required />
                                        </fieldset>

                                        <!-- Start/End Date -->
                                        <fieldset class="fieldset-resume_date fieldset-type-date half_field">
                                            <label class="ae_label"
                                                for="date_<?php echo $index; ?>"><?php esc_html_e('Start/End Date', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input ae_datepicker" type="text"
                                                name="candidate_experience[<?php echo $index; ?>][date]" id="date_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($experience['date']); ?>" />
                                            <label for="current_job">
                                                <input type="checkbox" class="current-job" id="current_job_<?php echo $index; ?>" />
                                                Present
                                            </label>
                                        </fieldset>

                                        <!-- Notes -->
                                        <fieldset class="fieldset-resume_notes fieldset-type-notes">
                                            <label class="ae_label"
                                                for="notes_<?php echo $index; ?>"><?php esc_html_e('Notes', 'wp-resume-manager'); ?></label>
                                            <textarea class="ae_input" name="candidate_experience[<?php echo $index; ?>][notes]"
                                                id="notes_<?php echo $index; ?>"
                                                rows="5"><?php echo esc_attr($experience['notes']); ?></textarea>
                                        </fieldset>

                                        <a href="javascript:void(0);"
                                            class="remove-experience"><?php esc_html_e('Remove Experience', 'wp-resume-manager'); ?></a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Show "Add Experience" button only if no entries exist -->
                        <?php if (empty($candidate_experience)) : ?>
                            <div class="no-experience-message">
                                <p><?php esc_html_e('No career history added yet.', 'wp-resume-manager'); ?></p>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="spacer-20"></div>

                    <div id="education" class="ae_form_card">
                        <h4 class="ae_form_card-title">
                            <?php esc_html_e('Education', 'wp-resume-manager'); ?>
                            <a href="javascript:void(0);"
                                id="add-education"><?php esc_html_e('Add Education', 'wp-resume-manager'); ?></a>
                        </h4>

                        <div id="education-history-repeater">
                            <!-- Hidden template for a blank education history entry -->
                            <div id="education-history-template" style="display: none;">
                                <div class="education-history-entry">
                                    <fieldset class="fieldset-resume_location fieldset-type-location">
                                        <label class="ae_label" for="new_location">Institution</label>
                                        <input class="ae_input" type="text" name="candidate_education[new][location]"
                                            id="new_location" value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_qualification fieldset-type-job-qualification half_field">
                                        <label class="ae_label" for="new_qualification">Certification(s)</label>
                                        <input class="ae_input" type="text" name="candidate_education[new][qualification]"
                                            id="new_qualification" value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_date fieldset-type-date half_field">
                                        <label class="ae_label" for="new_date">Start/End Date</label>
                                        <input class="ae_input" type="text" name="candidate_education[new][date]" id="new_date"
                                            value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_notes fieldset-type-notes">
                                        <label class="ae_label" for="new_notes">Notes</label>
                                        <textarea class="ae_input" name="candidate_education[new][notes]"
                                            id="new_notes"></textarea>
                                    </fieldset>

                                    <a href="javascript:void(0);" class="remove-education">Remove Experience</a>
                                </div>
                            </div>

                            <?php if (!empty($candidate_education)) : ?>
                                <?php foreach ($candidate_education as $index => $education) : ?>
                                    <div class="education-history-entry">
                                        <!-- Employer -->
                                        <fieldset class="fieldset-resume_location fieldset-type-location">
                                            <label class="ae_label"
                                                for="location_<?php echo $index; ?>"><?php esc_html_e('Institution', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="text"
                                                name="candidate_education[<?php echo $index; ?>][location]"
                                                id="location_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($education['location']); ?>" required />
                                        </fieldset>

                                        <!-- Job Title -->
                                        <fieldset class="fieldset-resume_qualification fieldset-type-job-qualification half_field">
                                            <label class="ae_label"
                                                for="qualification_<?php echo $index; ?>"><?php esc_html_e('Certification(s)', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="text"
                                                name="candidate_education[<?php echo $index; ?>][qualification]"
                                                id="qualification_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($education['qualification']); ?>" required />
                                        </fieldset>

                                        <!-- Start/End Date -->
                                        <fieldset class="fieldset-resume_date fieldset-type-date half_field">
                                            <label class="ae_label"
                                                for="date_<?php echo $index; ?>"><?php esc_html_e('Start/End Date', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input ae_datepicker" type="text"
                                                name="candidate_education[<?php echo $index; ?>][date]" id="date_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($education['date']); ?>" />
                                        </fieldset>

                                        <!-- Notes -->
                                        <fieldset class="fieldset-resume_notes fieldset-type-notes">
                                            <label class="ae_label"
                                                for="notes_<?php echo $index; ?>"><?php esc_html_e('Notes', 'wp-resume-manager'); ?></label>
                                            <textarea class="ae_input" name="candidate_education[<?php echo $index; ?>][notes]"
                                                id="notes_<?php echo $index; ?>"
                                                rows="5"><?php echo esc_attr($education['notes']); ?></textarea>
                                        </fieldset>

                                        <a href="javascript:void(0);"
                                            class="remove-education"><?php esc_html_e('Remove Education', 'wp-resume-manager'); ?></a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Show "Add Experience" button only if no entries exist -->
                        <?php if (empty($candidate_education)) : ?>
                            <div class="no-education-message">
                                <p><?php esc_html_e('No Education added yet.', 'wp-resume-manager'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="spacer-20"></div>

                    <div id="lisencesCertifications" class="ae_form_card">
                        <h4 class="ae_form_card-title">
                            <?php esc_html_e('Licences & Certifications', 'wp-resume-manager'); ?>
                            <a href="javascript:void(0);"
                                id="add-certification"><?php esc_html_e('Add Certification', 'wp-resume-manager'); ?></a>
                        </h4>

                        <div id="certifications-history-repeater">
                            <!-- Hidden template for a blank lisence history entry -->
                            <div id="certifications-history-template" style="display: none;">
                                <div class="certifications-history-entry">
                                    <fieldset class="fieldset-resume_licence_name fieldset-type-licence_name half_field">
                                        <label class="ae_label" for="licence_name">Certification Name</label>
                                        <input class="ae_input" type="text" name="resume_certifications[new][licence_name]"
                                            id="licence_name" value="" />
                                    </fieldset>

                                    <fieldset
                                        class="fieldset-resume_licence_issuer fieldset-type-job-licence_issuer half_field">
                                        <label class="ae_label" for="licence_issuer">Certification Issuer</label>
                                        <input class="ae_input" type="text" name="resume_certifications[new][licence_issuer]"
                                            id="licence_issuer" value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_issue_date fieldset-type-issue_date half_field">
                                        <label class="ae_label" for="issue_date">Certification Issue Date</label>
                                        <input class="ae_input" type="date" name="resume_certifications[new][issue_date]"
                                            id="issue_date" value="" />
                                    </fieldset>

                                    <fieldset class="fieldset-resume_expiry_date fieldset-type-expiry_date half_field">
                                        <label class="ae_label" for="expiry_date">Certification Expiry Date</label>
                                        <input class="ae_input" type="date" name="resume_certifications[new][expiry_date]"
                                            id="expiry_date" value="" />
                                    </fieldset>

                                    <a href="javascript:void(0);" class="remove-certification">Remove Certification</a>
                                </div>
                            </div>

                            <?php if (!empty($resume_certifications)) : ?>
                                <?php foreach ($resume_certifications as $index => $certification) : ?>
                                    <div class="certifications-history-entry">
                                        <!-- Employer -->
                                        <fieldset class="fieldset-resume_licence_name fieldset-type-licence_name half_field">
                                            <label class="ae_label"
                                                for="licence_name_<?php echo $index; ?>"><?php esc_html_e('Certification Name', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="text"
                                                name="resume_certifications[<?php echo $index; ?>][licence_name]"
                                                id="licence_name_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($certification['licence_name']); ?>" required />
                                        </fieldset>

                                        <!-- Job Title -->
                                        <fieldset class="fieldset-resume_licence_issuer fieldset-type-job-licence_issuer half_field">
                                            <label class="ae_label"
                                                for="licence_issuer<?php echo $index; ?>"><?php esc_html_e('Certification Issuer', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="text"
                                                name="resume_certifications[<?php echo $index; ?>][licence_issuer]"
                                                id="licence_issuer_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($certification['licence_issuer']); ?>" required />
                                        </fieldset>

                                        <!-- Start Date -->
                                        <fieldset class="fieldset-resume_issue_date fieldset-type-issue_date half_field">
                                            <label class="ae_label"
                                                for="issue_date_<?php echo $index; ?>"><?php esc_html_e('Certification Issue Date', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="date"
                                                name="resume_certifications[<?php echo $index; ?>][issue_date]"
                                                id="issue_date_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($certification['issue_date']); ?>" />
                                        </fieldset>

                                        <!-- End Date -->
                                        <fieldset class="fieldset-resume_expiry_date fieldset-type-expiry_date half_field">
                                            <label class="ae_label"
                                                for="expiry_date_<?php echo $index; ?>"><?php esc_html_e('Certification Expiry Date', 'wp-resume-manager'); ?></label>
                                            <input class="ae_input" type="date"
                                                name="resume_certifications[<?php echo $index; ?>][expiry_date]"
                                                id="expiry_date_<?php echo $index; ?>"
                                                value="<?php echo esc_attr($certification['expiry_date']); ?>" />
                                        </fieldset>

                                        <a href="javascript:void(0);"
                                            class="remove-certification"><?php esc_html_e('Remove Certification', 'wp-resume-manager'); ?></a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Show "Add Experience" button only if no entries exist -->
                        <?php if (empty($resume_certifications)) : ?>
                            <div class="no-certification-message">
                                <p><?php esc_html_e('No Certification added yet.', 'wp-resume-manager'); ?></p>
                            </div>
                        <?php endif; ?>


                    </div>

                    <div class="spacer-20"></div>

                    <div id="professionalSkills" class="ae_form_card">
                        <h4 class="ae_form_card-title">Professional Skills</h4>

                        <fieldset class="fieldset-resume_skills fieldset-type-skills">
                            <label class="ae_label"
                                for="resume_skills"><?php esc_html_e('Skills', 'wp-resume-manager'); ?></label>
                            <input type="text" id="resume_skills_input" placeholder="Type and press enter to add skills"
                                class="ae_input" />
                            <div id="skills-container">
                                <?php
                                // Display selected skills as tags
                                if (!empty($selected_skills)) {
                                    foreach ($selected_skills as $skill) {
                                        echo '<span class="skill-tag" data-skill="' . esc_html($skill) . '">' . esc_html($skill) . '<a href="#" class="remove-skill">x</a></span>';
                                    }
                                }
                                ?>
                            </div>
                            <input type="hidden" name="resume_skills" id="resume_skills"
                                value="<?php echo esc_attr(implode(',', $selected_skills)); ?>" />
                        </fieldset>
                    </div>

                    <!-- Save and Cancel Buttons -->
                    <div class="add_resume-form-btns">
                        <a href="javascript:void(0);" onclick="history.back();"
                            class="cancel-btn"><?php esc_attr_e('Cancel', 'wp-resume-manager'); ?></a>
                        <?php if ($resume_status === 'draft' || !$resume_id) : ?>
                            <input type="submit" name="resume_submit" class="button button-primary"
                                value="<?php esc_attr_e('Publish', 'wp-resume-manager'); ?>" />
                            <input type="submit" name="save_draft" class="button button-secondary"
                                value="<?php esc_attr_e('Save Draft', 'wp-resume-manager'); ?>" />
                        <?php else: ?>
                            <input type="submit" name="resume_submit" class="button button-primary"
                                value="<?php esc_attr_e('Save', 'wp-resume-manager'); ?>" />
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="add_resume-nav">
                <a href="#" class="add_resume-nav-link add_resume-employeeDetails">Employee Details</a>
                <a href="#" class="add_resume-nav-link add_resume-careerHistory">Career History</a>
                <a href="#" class="add_resume-nav-link add_resume-education">Education</a>
                <a href="#" class="add_resume-nav-link add_resume-lisencesCertifications">Licences & Certifications</a>
                <a href="#" class="add_resume-nav-link add_resume-professionalSkills">Professional Skills</a>
            </div>
        </div>
<?php
    }

    public function handle_form_submission_ajax()
    {
        // Verify the nonce for security
        check_ajax_referer('resume_nonce_action', 'security');

        // Sanitize the form inputs
        $resume_id = isset($_POST['resume_id']) ? intval($_POST['resume_id']) : 0;

        // Handle candidate photo upload
        $candidate_photo = '';
        if (isset($_FILES['candidate_photo']) && !empty($_FILES['candidate_photo']['name'])) {
            // Use WordPress' wp_handle_upload function to upload the file
            $uploaded_photo = wp_handle_upload($_FILES['candidate_photo'], [
                'test_form' => false, // This is important for file uploads via forms
            ]);

            // Check for upload errors
            if (isset($uploaded_photo['url']) && !isset($uploaded_photo['error'])) {
                $candidate_photo = esc_url($uploaded_photo['url']); // Save the uploaded file URL
            } else {
                // Return an error if the upload fails
                wp_send_json_error(['message' => __('Photo upload failed: ', 'wp-resume-manager') . $uploaded_photo['error']]);
                return;
            }
        }



        $resume_title = sanitize_text_field($_POST['resume_title']);
        $resume_description = wp_kses_post($_POST['resume_description']);
        $professional_title = sanitize_text_field($_POST['professional_title']);
        $right_to_work = sanitize_text_field($_POST['right_to_work']);

        // Handle resume file upload
        $resume_file = '';
        if (!empty($_FILES['resume_file']['name'])) {
            $uploaded_file = wp_handle_upload($_FILES['resume_file'], array('test_form' => false));

            if (isset($uploaded_file['file'])) {
                $resume_file = $uploaded_file['url'];  // Save the uploaded file URL
            } else {
                wp_send_json_error(['message' => __('File upload failed.', 'wp-resume-manager')]);
                return;
            }
        }

        // Determine post status (draft or publish)
        $post_status = isset($_POST['save_draft']) ? 'draft' : 'publish';

        // Sanitize and process experience data
        $candidate_experience = [];
        if (isset($_POST['candidate_experience']) && is_array($_POST['candidate_experience'])) {
            foreach ($_POST['candidate_experience'] as $experience) {
                // Skip empty entries
                if (!empty($experience['employer']) || !empty($experience['job_title'])) {
                    $candidate_experience[] = [
                        'employer'   => sanitize_text_field($experience['employer']),
                        'job_title'  => sanitize_text_field($experience['job_title']),
                        'date'       => sanitize_text_field($experience['date']),
                        'notes'      => wp_kses_post($experience['notes']),
                    ];
                }
            }
        }

        // Sanitize and process education data
        $candidate_education = [];
        if (isset($_POST['candidate_education']) && is_array($_POST['candidate_education'])) {
            foreach ($_POST['candidate_education'] as $education) {
                // Skip empty entries
                if (!empty($education['location']) || !empty($education['qualification'])) {
                    $candidate_education[] = [
                        'location'   => sanitize_text_field($education['location']),
                        'qualification'  => sanitize_text_field($education['qualification']),
                        'date'       => sanitize_text_field($education['date']),
                        'notes'      => wp_kses_post($education['notes']),
                    ];
                }
            }
        }

        // Sanitize and process certification data
        $resume_certifications = [];
        if (isset($_POST['resume_certifications']) && is_array($_POST['resume_certifications'])) {
            foreach ($_POST['resume_certifications'] as $certification) {
                // Skip empty entries
                if (!empty($certification['licence_name']) || !empty($certification['licence_issuer'])) {
                    $resume_certifications[] = [
                        'licence_name'   => sanitize_text_field($certification['licence_name']),
                        'licence_issuer'  => sanitize_text_field($certification['licence_issuer']),
                        'issue_date'       => sanitize_text_field($certification['issue_date']),
                        'expiry_date'       => sanitize_text_field($certification['expiry_date']),
                    ];
                }
            }
        }

        // Sanitize and process the skills
        $resume_skills = isset($_POST['resume_skills']) ? sanitize_text_field($_POST['resume_skills']) : '';
        $skills_array = explode(',', $resume_skills);



        // Prepare post data
        $post_data = [
            'post_title'   => $resume_title,  // Set the post title as the resume title
            'post_content' => $resume_description,  // Set the post content as the resume description
            'post_status'  => $post_status,
            'post_type'    => 'resume',
        ];

        // Insert or update the resume post
        if ($resume_id) {
            $post_data['ID'] = $resume_id;
            $result = wp_update_post($post_data);  // Update existing post
        } else {
            $result = wp_insert_post($post_data);  // Insert new post
        }

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => 'Error: ' . $result->get_error_message()]);
        } else {
            // If a new candidate photo was uploaded, save it to the post meta
            if (!empty($candidate_photo)) {
                update_post_meta($result, '_candidate_photo', $candidate_photo);
            } elseif ($resume_id && isset($_POST['candidate_photo_current'])) {
                // If no new photo is uploaded, retain the existing photo URL
                update_post_meta($resume_id, '_candidate_photo', sanitize_text_field($_POST['candidate_photo_current']));
            }
            // Save the professional title and right to work
            update_post_meta($result, '_candidate_title', $professional_title);
            update_post_meta($result, '_candidate_right_to_work', $right_to_work);

            // Save candidate experience meta field
            update_post_meta($result, '_candidate_experience', $candidate_experience);

            // Save candidate education meta field
            update_post_meta($result, '_candidate_education', $candidate_education);

            // Save candidate certification meta field
            update_post_meta($result, '_resume_certifications', $resume_certifications);

            // Assign skills to the resume post
            if ($skills_array) {
                wp_set_object_terms($result, $skills_array, 'resume_skill');
            }

            // Save the resume file URL as post meta
            if ($resume_file) {
                update_post_meta($result, '_resume_file', $resume_file);
            }

            // Return a success message and the redirect URL
            $redirect_url = add_query_arg('resume_id', $result, esc_url_raw($_POST['_wp_http_referer']));
            wp_send_json_success(['message' => 'Resume saved successfully!', 'redirect_url' => $redirect_url]);
        }

        wp_die(); // End the AJAX process
    }


    function fetch_skills()
    {
        check_ajax_referer('resume_nonce_action', 'security');

        $term = sanitize_text_field($_GET['term']);
        $skills = get_terms(array(
            'taxonomy' => 'resume_skill',
            'name__like' => $term,
            'hide_empty' => false,
        ));

        $skill_names = array();
        foreach ($skills as $skill) {
            $skill_names[] = $skill->name;
        }

        wp_send_json($skill_names);
    }


    public function enqueue_assets()
    {
        // Get the main plugin instance
        global $adventists_employment_plugin;

        // Only enqueue assets if the shortcode is used on this page
        if (
            !isset($adventists_employment_plugin) || !method_exists($adventists_employment_plugin, 'is_shortcode_used') ||
            $adventists_employment_plugin->is_shortcode_used('resume_form')
        ) {

            wp_enqueue_script('wp-job-manager-term-multiselect');
            // wp_enqueue_script('jquery-ui-datepicker');
            // wp_enqueue_style('jquery-ui');
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

            // Enqueue Daterangepicker CSS and JS
            wp_enqueue_style('daterangepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
            wp_enqueue_script('moment-js', 'https://cdn.jsdelivr.net/npm/moment/min/moment.min.js', array('jquery'), null, true);
            wp_enqueue_script('daterangepicker-js', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array('jquery', 'moment-js'), null, true);

            wp_enqueue_style('resume-form-css', plugin_dir_url(__FILE__) . '../css/resume-form.css');
            wp_enqueue_script('resume-form-js', plugin_dir_url(__FILE__) . '../js/resume-form.js', array('jquery'), null, true);

            // Localize the AJAX URL and nonce
            wp_localize_script('resume-form-js', 'resumeFormAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('resume_nonce_action'),
            ));
        }
    }
}

new ResumeForm();
