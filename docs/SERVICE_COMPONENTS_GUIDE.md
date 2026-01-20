# Service Components Usage Guide

## Overview

Service components are reusable content blocks that can be added to service pages. They include videos, checklists, and timelines that are automatically rendered in the page content.

## Available Components

### 1. Video Component
Embeds videos from YouTube or Vimeo with optional captions.

### 2. Checklist Component
Displays a styled list of items with checkmark icons.

### 3. Timeline Component
Shows process steps or phases in chronological order.

## Adding Components via Meta Box

### Step 1: Edit Service Post
1. Go to **Services** → **All Services**
2. Edit the service post where you want to add components
3. Scroll down to the **Service Components** meta box

### Step 2: Add Component
1. Click one of the component buttons:
   - **+ Add Video** - For video embeds
   - **+ Add Checklist** - For checklist lists
   - **+ Add Timeline** - For process timelines

### Step 3: Configure Component

#### Video Component
- **Video URL**: Enter YouTube or Vimeo URL (e.g., `https://youtube.com/watch?v=...`)
- **Video Caption** (Optional): Add a caption below the video
- **Custom Thumbnail** (Optional): Select a custom thumbnail image

#### Checklist Component
- **Checklist Title** (Optional): Section heading
- **Checklist Items**: Click **+ Add Item** to add items, then enter text for each item

#### Timeline Component
- **Timeline Title** (Optional): Section heading
- **Timeline Steps**: Click **+ Add Step** to add steps
  - Enter **Step title** and **Step description** for each step

### Step 4: Reorder Components
- Use **↑** and **↓** buttons to reorder components
- Components render in the order they appear in the meta box

### Step 5: Remove Component
- Click **Remove** button to delete a component

### Step 6: Save
- Click **Update** or **Publish** to save changes
- Components will automatically appear in the service page content

## Using Shortcodes

Components can also be added directly in post content using shortcodes.

### Video Shortcode

```
[service_video url="https://youtube.com/watch?v=abc123" caption="Optional caption text"]
```

**Attributes:**
- `url` (required) - Video URL (YouTube or Vimeo)
- `caption` (optional) - Caption text below video
- `thumbnail` (optional) - Attachment ID for custom thumbnail

**Example:**
```
[service_video url="https://youtube.com/watch?v=dQw4w9WgXcQ" caption="Watch our process in action"]
```

### Checklist Shortcode

```
[service_checklist title="Our Services Include" items="Item 1|Item 2|Item 3"]
```

Or with content:
```
[service_checklist title="Our Services Include"]Item 1|Item 2|Item 3[/service_checklist]
```

**Attributes:**
- `title` (optional) - Section title
- `items` (required) - Pipe-separated list of items

**Example:**
```
[service_checklist title="What We Offer" items="Comprehensive assessment|Detailed reporting|Ongoing support|Expert consultation"]
```

### Timeline Shortcode

```
[service_timeline title="Our Process" steps='[{"step":"Step 1","description":"Description 1"},{"step":"Step 2","description":"Description 2"}]']
```

**Attributes:**
- `title` (optional) - Section title
- `steps` (required) - JSON array of step objects

**Example:**
```
[service_timeline title="Our Process" steps='[{"step":"Initial Consultation","description":"We meet to understand your needs"},{"step":"Site Assessment","description":"Our team conducts a thorough evaluation"},{"step":"Report Delivery","description":"You receive detailed findings"}]']
```

## Component Rendering

### Automatic Rendering
Components added via the meta box are automatically rendered in the service page template after the main content and before related services.

### Manual Rendering
To render components programmatically:

```php
<?php
// Render all components for current post
echo ehs_render_service_components();

// Render components for specific post
echo ehs_render_service_components($post_id);
?>
```

### Individual Component Rendering

```php
<?php
// Render video component
$video_data = array(
    'type' => 'video',
    'video_url' => 'https://youtube.com/watch?v=...',
    'video_caption' => 'Optional caption'
);
echo ehs_render_service_video($video_data);

// Render checklist component
$checklist_data = array(
    'type' => 'checklist',
    'checklist_title' => 'Our Services',
    'checklist_items' => array('Item 1', 'Item 2', 'Item 3')
);
echo ehs_render_service_checklist($checklist_data);

// Render timeline component
$timeline_data = array(
    'type' => 'timeline',
    'timeline_title' => 'Our Process',
    'timeline_items' => array(
        array('step' => 'Step 1', 'description' => 'Description 1'),
        array('step' => 'Step 2', 'description' => 'Description 2')
    )
);
echo ehs_render_service_timeline($timeline_data);
?>
```

## Using in Elementor

Components can be used in Elementor via the **Shortcode** widget:

1. Add a **Shortcode** widget to your page
2. Paste the shortcode syntax (see examples above)
3. The component will render with full styling

## Styling

All components follow the EHS design system:
- **Colors**: Navy (#003366) and Gold (#FFB81C)
- **Typography**: Maven Pro font family
- **Spacing**: Consistent spacing scale
- **Responsive**: Mobile-friendly layouts

### Custom CSS Classes

Components use these CSS classes for styling:
- `.service-component` - Base component container
- `.service-component-video` - Video component
- `.service-component-checklist` - Checklist component
- `.service-component-timeline` - Timeline component

## Best Practices

1. **Use Meta Box for Most Cases**: The meta box provides the easiest way to manage components
2. **Use Shortcodes for Flexibility**: Shortcodes are useful when you need components in specific locations or in Elementor
3. **Keep Titles Concise**: Component titles should be short and descriptive
4. **Limit Checklist Items**: Keep checklist items to 4-8 items for best readability
5. **Timeline Steps**: 3-6 steps work best for timelines
6. **Video Placement**: Place videos after introductory content, not at the very top

## Troubleshooting

### Components Not Showing
- Verify the service post is published
- Check that components are saved in the meta box
- Clear WordPress cache if using caching plugins

### Video Not Embedding
- Verify the URL is a valid YouTube or Vimeo URL
- Check that the URL format is correct
- Try the full URL format: `https://youtube.com/watch?v=...`

### Shortcode Not Working
- Ensure shortcodes are enabled in your content
- Check for typos in shortcode syntax
- Verify attribute values are properly quoted

### Styling Issues
- Clear browser cache
- Verify theme CSS is loading
- Check for CSS conflicts with other plugins

## Technical Details

### Data Storage
Components are stored in the `service_components` post meta field as a JSON string.

### Sanitization
All component data is sanitized on save:
- URLs are validated and escaped
- Text fields are sanitized
- Arrays are validated and cleaned

### Security
- All output is escaped using WordPress functions
- Nonces are used for meta box saves
- User capabilities are checked before saving

## Support

For issues or questions:
1. Check this documentation
2. Review the component audit: `docs/service-components-audit.md`
3. Check theme files in `inc/frontend/service-components-*.php`
