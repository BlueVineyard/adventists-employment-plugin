# System Patterns: Adventists Employment Plugin

## System Architecture
The Adventists Employment Plugin follows a modular WordPress plugin architecture with clear separation of concerns:

```mermaid
flowchart TD
    Main[Main Plugin Class] --> SC[Shortcode Registration]
    Main --> Auth[Authentication Handling]
    Main --> Redir[Redirection Logic]
    Main --> Logout[Custom Logout]
    
    SC --> JF[Job Form]
    SC --> RF[Resume Form]
    SC --> BF[Blog Filter]
    SC --> RP[Related Posts]
    SC --> TB[Top Blogs]
    SC --> SF[Signup Form]
    
    JF --> Templates[Templates]
    RF --> Templates
    BF --> Templates
    RP --> Templates
    TB --> Templates
    SF --> Templates
    
    JF --> CSS[CSS Styling]
    RF --> CSS
    BF --> CSS
    RP --> CSS
    TB --> CSS
    SF --> CSS
    
    JF --> JS[JavaScript]
    RF --> JS
    BF --> JS
    TB --> JS
    SF --> JS
    
    JF --> AJAX[AJAX Handlers]
    RF --> AJAX
    BF --> AJAX
    
    JF --> Maps[Google Maps Integration]
    Maps --> ACF[ACF Google Maps Field]
```

## Key Technical Decisions

### 1. Class-Based Component Structure
Each major feature is implemented as a separate PHP class:
- `JobListingForm` - Handles job posting functionality
- `ResumeForm` - Manages resume submissions
- `BlogFilter` - Controls blog content filtering
- `RelatedPosts` - Manages related content display
- `TopBlogs` - Handles featured blog content

This approach provides:
- Clear separation of concerns
- Maintainable, modular codebase
- Easy extension for new features

### 2. Shortcode Implementation
All user-facing components are implemented as WordPress shortcodes, allowing:
- Flexible placement within any WordPress page or post
- Easy integration with page builders
- Consistent rendering across the site

### 3. AJAX-Based Form Handling
Forms use AJAX for submission, providing:
- Better user experience with no page reloads
- Immediate feedback on form submission
- Improved error handling

### 4. Custom Post Types
The plugin leverages WordPress custom post types for:
- Job listings (`job_listing`)
- Employer profiles (`employer-dashboard`)
- Resume submissions

### 5. Metadata Management
Extensive use of post meta for storing:
- Job details (salary, location, etc.)
- Company information (name, website, logo)
- Application deadlines
- Custom form fields

## Component Relationships

### Logo Management Flow
```mermaid
flowchart LR
    EP[Employer Profile] -->|stores| Logo[Company Logo]
    JF[Job Form] -->|selects| EP
    JF -->|retrieves| Logo
    JF -->|displays| Logo
    JF -->|saves with| JL[Job Listing]
```

### Form Submission Flow
```mermaid
flowchart TD
    Form[Form UI] -->|submits via AJAX| Handler[Form Handler]
    Handler -->|validates| Data[Form Data]
    Handler -->|creates/updates| Post[WordPress Post]
    Handler -->|stores| Meta[Post Meta]
    Handler -->|sends| Response[JSON Response]
    Response -->|updates| UI[User Interface]
```

## Design Patterns

1. **Factory Pattern**: The main plugin class acts as a factory, initializing the various component classes.

2. **Singleton Pattern**: Each component class is instantiated once and registered with WordPress hooks.

3. **Template Pattern**: Component classes define the structure while template files handle the presentation.

4. **Observer Pattern**: WordPress hooks and filters are used extensively to allow components to react to events.

5. **MVC-like Structure**:
   - Models: WordPress posts and meta data
   - Views: Template files in the templates directory
   - Controllers: PHP classes in the includes directory

## Google Maps Integration

The job form includes Google Maps integration for address selection and geocoding:

```mermaid
flowchart TD
    Input[Address Input Field] -->|User Types| Autocomplete[Google Maps Autocomplete]
    Autocomplete -->|Place Selected| Extract[Extract Address & Coordinates]
    Extract -->|Store in Hidden Field| Form[Form Data]
    Form -->|Submit| AJAX[AJAX Handler]
    AJAX -->|Format Data| ACF[ACF Google Maps Field]
    ACF -->|Store| Database[WordPress Database]
    Database -->|Retrieve| Backend[WordPress Backend]
    Backend -->|Render| Map[Google Map Display]
```

### Implementation Details

1. **Frontend Implementation**:
   - Uses Google Maps Places Autocomplete API for address suggestions
   - Extracts formatted address and coordinates (latitude/longitude) when a place is selected
   - Stores coordinates in a hidden field for submission

2. **Data Storage**:
   - Saves data in the format expected by ACF Google Maps field
   - Combines address text and coordinates in a structured array: `{address: "...", lat: "...", lng: "..."}`
   - This format ensures proper rendering of the map in the WordPress backend

3. **Data Retrieval**:
   - When editing an existing job, retrieves the ACF Google Maps field data
   - Extracts the address for display in the input field
   - Formats coordinates for the hidden field
