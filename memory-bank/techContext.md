# Technical Context: Adventists Employment Plugin

## Technologies Used

### Core Technologies
- **PHP**: Primary server-side language for WordPress plugin development
- **WordPress**: Content management system and plugin framework
- **MySQL**: Database for storing plugin data
- **JavaScript/jQuery**: Client-side scripting for interactive features
- **CSS**: Styling for all plugin components
- **AJAX**: Asynchronous data handling for form submissions and dynamic content

### WordPress Specific
- **WordPress Plugin API**: Hooks, filters, and shortcodes
- **WordPress Custom Post Types**: For job listings and employer profiles
- **WordPress Taxonomies**: For categorizing jobs (type, category, location)
- **WordPress Meta API**: For storing custom fields and data
- **WordPress TinyMCE**: Rich text editor integration for job descriptions

### Libraries & Dependencies
- **jQuery UI**: For datepicker and other UI components
- **wp-job-manager-term-multiselect**: For taxonomy term selection
- **wp-job-manager-datepicker**: For date selection fields

## Development Setup

### Plugin Structure
```
adventists-employment-plugin/
├── adventists-employment-plugin.php  # Main plugin file
├── css/                             # Stylesheet directory
│   ├── blog-filter.css
│   ├── job-form.css
│   ├── related-posts.css
│   ├── resume-form.css
│   ├── shortcode1.css
│   └── sign-up-form.css
├── includes/                        # PHP class files
│   ├── class-blog-filter.php
│   ├── class-job-form.php
│   ├── class-related-posts.php
│   ├── class-resume-form.php
│   ├── class-shortcode1.php
│   └── class-signup-form.php
├── js/                              # JavaScript files
│   ├── blog-filter.js
│   ├── custom-logout.js
│   ├── job-form.js
│   ├── resume-form.js
│   └── sign-up-form.js
└── templates/                       # Template files
    ├── blog-filter-template.php
    ├── related-posts-template.php
    ├── shortcode1-template.php
    └── top-blogs-template.php
```

### Development Workflow
1. **Local Development**: Plugin is developed in a local WordPress environment
2. **Version Control**: Changes tracked using Git
3. **Testing**: Manual testing of features in WordPress admin and frontend
4. **Deployment**: Plugin files uploaded to production WordPress installation

## Technical Constraints

### WordPress Compatibility
- Must maintain compatibility with WordPress core
- Should follow WordPress coding standards and best practices
- Must work with standard WordPress themes

### Performance Considerations
- Minimize database queries for better performance
- Optimize JavaScript and CSS loading
- Consider caching strategies for frequently accessed data

### Security Requirements
- Input validation and sanitization for all form submissions
- Proper nonce verification for AJAX requests
- Capability checks for administrative actions
- Secure handling of file uploads (for logos)

## Dependencies

### WordPress Core Dependencies
- WordPress version 5.0 or higher
- PHP 7.0 or higher
- MySQL 5.6 or higher

### Plugin Dependencies
- Advanced Custom Fields (ACF): Used for custom fields in employer profiles
- WP Job Manager (optional): Some components leverage WP Job Manager functionality

### External Services
- None currently required, but the plugin architecture supports integration with:
  - Job board aggregators
  - Email notification services
  - Application tracking systems

## Technical Debt & Considerations
- Some CSS could benefit from standardization across components
- JavaScript could be further modularized
- Additional error handling and validation could improve robustness
- Mobile responsiveness should be reviewed and enhanced
