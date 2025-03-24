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
- **jQuery UI**: For datepicker, autocomplete, and other UI components
- **Google Maps API**: For address selection and geocoding in job listings
  - Uses Places library for address autocomplete
  - Integrated with ACF Google Maps field for backend display
  - Requires proper data formatting: `{address: "...", lat: "...", lng: "..."}`
- **Advanced Custom Fields (ACF)**: For custom field management
  - ACF Google Maps field used for storing and displaying location data
  - Provides interactive map display in the WordPress admin
- **Moment.js**: For date handling in resume forms
- **Daterangepicker**: For date range selection in resume experience entries
- **wp-job-manager-term-multiselect**: For taxonomy term selection
- **wp-job-manager-datepicker**: For date selection fields

## Development Setup

### Plugin Structure
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
├── memory-bank/                     # Documentation directory
│   ├── .clinerules                  # Project-specific patterns
│   ├── activeContext.md             # Current work focus
│   ├── productContext.md            # Product purpose and goals
│   ├── progress.md                  # Implementation status
│   ├── projectbrief.md              # Project overview
│   ├── systemPatterns.md            # Architecture documentation
│   └── techContext.md               # Technical context
└── templates/                       # Template files
    ├── blog-filter-template.php     # Blog filter display template
    ├── related-posts-template.php   # Related posts display template
    ├── shortcode1-template.php      # Generic shortcode template
    └── top-blogs-template.php       # Top blogs display template
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
- **Form Validation**: Client-side validation could be enhanced for better user feedback
- **Mobile Responsiveness**: Several components need optimization for mobile devices
- **JavaScript Organization**: Resume form JavaScript is complex and could benefit from modularization
- **CSS Standardization**: Styles could be more consistent across components
- **Error Handling**: More comprehensive error handling for AJAX requests
- **Performance Optimization**: 
  - Large logos and images could be optimized
  - AJAX requests could be optimized or batched
  - Database queries could be optimized for large datasets
- **Security Enhancements**:
  - Additional sanitization for form inputs
  - More comprehensive capability checks
  - Enhanced file upload validation
