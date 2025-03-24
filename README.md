# Adventists Employment Plugin

A comprehensive WordPress plugin designed to enhance the functionality of the Adventists Employment website, providing various shortcodes and features to manage job listings, employer profiles, and user interactions on the employment platform.

## Overview

The Adventists Employment Plugin creates a specialized job board and employment platform tailored specifically for Adventist organizations and job seekers. It addresses the unique needs of the Adventist community by providing a centralized location for employment opportunities within Adventist institutions.

## Features

### Job Listing Management
- Complete job form with comprehensive fields for job details, criteria, and company information
- AJAX-based form submission with draft/publish functionality
- Google Maps integration for address selection
- Rich text editors for job descriptions
- Application deadline management
- External application option
- Job taxonomies (type, category, location)

### Employer Profile Management
- Employer profile storage with company details
- Logo management and display
- Company website integration
- Automatic retrieval of employer details when creating job listings

### Resume Submission
- Comprehensive resume form with personal details, experience, education, and skills
- Dynamic sections for career history with date ranges
- Education history tracking
- Certifications and licenses management
- Skills tagging with autocomplete
- Candidate photo upload
- Resume file upload
- Draft/publish status management

### Blog and Content Features
- Blog filtering by category with AJAX pagination
- Related posts display based on categories or company
- Top blogs feature for featured content
- Responsive grid layout for blog posts

### User Authentication
- Custom signup forms for candidates and employers
- Role-based access control
- Dashboard redirection based on user role
- Custom logout functionality
- Admin toolbar hiding for non-administrators

## Installation

1. Upload the `adventists-employment-plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure any required settings

## Usage

### Shortcodes

The plugin provides several shortcodes that can be used on any WordPress page or post:

#### Job Form
```
[job_form]
```
Displays a form for employers to submit job listings. The form includes fields for job title, description, requirements, company information, and more.

#### Resume Form
```
[resume_form]
```
Displays a form for job seekers to submit their resumes. The form includes sections for personal information, work experience, education, skills, and more.

#### Blog Filter
```
[blog_filter]
```
Displays a filterable list of blog posts with category-based filtering and AJAX pagination.

#### Related Posts
```
[related_posts]
```
Displays related posts based on categories or company name.

#### Top Blogs
```
[top_blogs]
```
Displays featured blog content in a visually appealing layout.

### Integration with WordPress

The plugin integrates with WordPress using:
- Custom post types for job listings, employer profiles, and resume submissions
- WordPress taxonomies for categorizing jobs
- WordPress meta API for storing custom fields and data
- Advanced Custom Fields (ACF) for custom field management
- AJAX for form submissions and dynamic content loading

## Technical Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Advanced Custom Fields (ACF) plugin
- Google Maps API key (for address selection and geocoding in job listings)

## Structure

```
adventists-employment-plugin/
├── adventists-employment-plugin.php  # Main plugin file
├── css/                             # Stylesheet directory
│   ├── blog-filter.css              # Styles for blog filtering
│   ├── class-job-form.css           # Additional job form styles
│   ├── job-form.css                 # Styles for job submission form
│   ├── related-posts.css            # Styles for related posts display
│   ├── resume-form.css              # Styles for resume submission form
│   ├── shortcode1.css               # Styles for shortcode1
│   ├── sign-up-form.css             # Styles for user signup forms
│   └── top-blogs.css                # Styles for top blogs display
├── includes/                        # PHP class files
│   ├── class-blog-filter.php        # Blog filtering functionality
│   ├── class-job-form.php           # Job submission form handling
│   ├── class-related-posts.php      # Related posts display
│   ├── class-resume-form.php        # Resume submission form handling
│   ├── class-shortcode1.php         # Generic shortcode implementation
│   ├── class-signup-form.php        # User registration functionality
│   └── class-top-blogs.php          # Top blogs display
├── js/                              # JavaScript files
│   ├── blog-filter.js               # Blog filtering interactivity
│   ├── custom-logout.js             # Custom logout functionality
│   ├── job-form.js                  # Job form validation and submission
│   ├── resume-form.js               # Resume form with dynamic sections
│   ├── sign-up-form.js              # Signup form handling
│   └── top-blogs.js                 # Top blogs functionality
└── templates/                       # Template files
    ├── blog-filter-template.php     # Blog filter display template
    ├── related-posts-template.php   # Related posts display template
    ├── shortcode1-template.php      # Generic shortcode template
    └── top-blogs-template.php       # Top blogs display template
```

## Key Workflows

### Job Posting Workflow
1. Employer logs in with appropriate credentials
2. Navigates to the job submission page containing the `[job_form]` shortcode
3. Fills out the job details, including title, description, requirements, and company information
4. Selects their organization from the employer dropdown, which automatically retrieves the company logo
5. Uses Google Maps integration to select the job location
6. Sets application deadline and submission method
7. Chooses to save as draft or publish immediately
8. Form is submitted via AJAX without page reload
9. Job listing is created and displayed on the site

### Resume Submission Workflow
1. Job seeker logs in with appropriate credentials
2. Navigates to the resume submission page containing the `[resume_form]` shortcode
3. Fills out personal information, including name, contact details, and uploads a photo
4. Adds work experience entries with company name, position, date range, and description
5. Adds education history with institution, degree, date range, and description
6. Adds certifications, licenses, and skills
7. Uploads resume document
8. Chooses to save as draft or publish immediately
9. Form is submitted via AJAX without page reload
10. Resume is created and stored in the system

### Blog Filtering Workflow
1. User visits a page containing the `[blog_filter]` shortcode
2. Views the initial list of blog posts
3. Selects a category to filter the posts
4. Posts are filtered via AJAX without page reload
5. User can navigate through paginated results

## Author
Rohan T George

## Version
1.0
