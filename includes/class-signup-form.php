<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AE_Signup_Form
{

    public function __construct()
    {
        // Register the signup form shortcode
        add_shortcode('ae_signup_form', [$this, 'render_signup_form']);

        // Restrict dashboard access for candidates and employers
        add_action('init', [$this, 'ae_restrict_dashboard_access']);

        // Handle registration logic
        add_action('init', [$this, 'handle_registration']);

        // Handle Role logic
        add_action('init', [$this, 'register_user_roles']);

        // Enqueue styles and scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function render_signup_form()
    {
        ob_start();
?>
        <div id="signup-role-selection">
            <div id="candidate-div" class="role-selection" style="cursor:pointer;">
                <h2>Candidate Signup</h2>
            </div>
            <div id="employer-div" class="role-selection" style="cursor:pointer;">
                <h2>Employer Signup</h2>
            </div>
        </div>

        <!-- Candidate Form -->
        <form id="candidate-form" action="" method="post" style="display:none;">
            <h2>Candidate Signup Form</h2>
            <label for="candidate_username">Candidate Username:</label>
            <input type="text" name="candidate_username" id="candidate_username" required>

            <label for="candidate_email">Candidate Email:</label>
            <input type="email" name="candidate_email" id="candidate_email" required>

            <input type="hidden" name="user_role" value="candidate">
            <input type="submit" name="submit_candidate_signup" value="Sign Up">
        </form>

        <!-- Employer Form -->
        <form id="employer-form" action="" method="post" style="display:none;">
            <h2>Employer Signup Form</h2>
            <label for="employer_username">Employer Username:</label>
            <input type="text" name="employer_username" id="employer_username" required>

            <label for="employer_email">Employer Email:</label>
            <input type="email" name="employer_email" id="employer_email" required>

            <input type="hidden" name="user_role" value="employer">
            <input type="submit" name="submit_employer_signup" value="Sign Up">
        </form>
<?php
        return ob_get_clean();
    }


    public function handle_registration()
    {
        if (isset($_POST['submit_candidate_signup']) || isset($_POST['submit_employer_signup'])) {

            // Sanitize the input values
            $role = sanitize_text_field($_POST['user_role']);
            $username = sanitize_text_field($_POST[$role . '_username']);
            $email = sanitize_email($_POST[$role . '_email']);

            // Check for missing username or email
            if (empty($username) || empty($email)) {
                wp_die('Username and email are required.');
            }

            // Check if the username or email is already registered
            if (username_exists($username) || email_exists($email)) {
                wp_die('Username or email already exists. Please try a different one.');
            }

            // Use WordPress's default registration function
            $user_id = wp_create_user($username, wp_generate_password(), $email);

            if (!is_wp_error($user_id)) {
                // Set user role
                $user = new WP_User($user_id);
                $user->set_role($role);

                // WordPress will automatically send an email to the user with a link to set their password
                wp_new_user_notification($user_id, null, 'user');

                // Redirect to a confirmation page or thank you page
                wp_redirect(home_url('/thank-you-for-signing-up'));
                exit;
            } else {
                wp_die('Error creating user: ' . $user_id->get_error_message());
            }
        }
    }


    public function ae_restrict_dashboard_access()
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (in_array('candidate', $user->roles) || in_array('employer', $user->roles)) {
                if (is_admin() && !defined('DOING_AJAX')) {
                    wp_redirect(home_url());
                    exit;
                }
                show_admin_bar(false);
            }
        }
    }


    public function register_user_roles()
    {
        if (!get_role('candidate')) {
            add_role('candidate', __('Candidate'), ['read' => true]);
        }

        if (!get_role('employer')) {
            add_role('employer', __('Employer'), ['read' => true]);
        }
    }

    public function enqueue_assets()
    {
        // Get the main plugin instance
        global $adventists_employment_plugin;

        // Only enqueue assets if the shortcode is used on this page
        if (
            !isset($adventists_employment_plugin) || !method_exists($adventists_employment_plugin, 'is_shortcode_used') ||
            $adventists_employment_plugin->is_shortcode_used('ae_signup_form')
        ) {

            wp_enqueue_style('sign-up-form-css', plugin_dir_url(__FILE__) . '../css/sign-up-form.css');
            wp_enqueue_script('sign-up-form-js', plugin_dir_url(__FILE__) . '../js/sign-up-form.js', array('jquery'), null, true);
        }
    }
}

new AE_Signup_Form();
