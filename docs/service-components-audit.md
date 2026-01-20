# Service Page Components Audit

## Overview
This document catalogs the reusable components found on service pages at ehsanalytical.com that need to be implemented as shortcodes in the new dev site.

## Component Types Identified

### 1. Video Embed Component
**Purpose:** Embed videos (YouTube, Vimeo, etc.) within service page content

**Current Usage:**
- Some service pages include embedded videos
- Videos are typically placed mid-content or after introductory sections
- Responsive video embeds with optional captions

**Required Fields:**
- `video_url` - Full video URL (YouTube/Vimeo)
- `video_caption` - Optional caption text below video
- `video_thumbnail` - Optional custom thumbnail (attachment ID)

**Implementation Notes:**
- Auto-detect video platform from URL
- Generate appropriate embed code
- Support YouTube, Vimeo, and other common platforms
- Responsive 16:9 aspect ratio container

---

### 2. Checklist Component
**Purpose:** Display styled lists of services, benefits, or features with checkmark icons

**Current Usage:**
- Common on service pages showing "What We Offer" or "Our Services Include"
- Bullet lists with checkmark styling
- Often appears in two-column layouts on desktop

**Required Fields:**
- `checklist_title` - Optional section title
- `checklist_items` - Array of checklist items (strings)

**Implementation Notes:**
- Use checkmark SVG icons matching design system
- Support single or two-column layout
- Responsive: single column on mobile
- Navy/gold color scheme

---

### 3. Timeline Component
**Purpose:** Display process steps or phases in chronological order

**Current Usage:**
- Shows service delivery process
- Step-by-step workflow visualization
- Vertical timeline with connecting lines

**Required Fields:**
- `timeline_title` - Optional section title
- `timeline_items` - Array of timeline steps, each with:
  - `step` - Step title/name
  - `description` - Step description text

**Implementation Notes:**
- Vertical timeline layout
- Step indicators (numbered or icon-based)
- Connecting lines between steps
- Responsive: horizontal scroll on mobile or stacked

---

## Component Data Structure

All components will be stored in a single meta field `service_components` as a JSON array:

```json
[
  {
    "type": "video",
    "video_url": "https://youtube.com/watch?v=...",
    "video_caption": "Watch our process in action",
    "video_thumbnail": 123
  },
  {
    "type": "checklist",
    "checklist_title": "Our Services Include",
    "checklist_items": [
      "Comprehensive site assessment",
      "Detailed reporting",
      "Ongoing support"
    ]
  },
  {
    "type": "timeline",
    "timeline_title": "Our Process",
    "timeline_items": [
      {
        "step": "Initial Consultation",
        "description": "We meet with you to understand your needs"
      },
      {
        "step": "Site Assessment",
        "description": "Our team conducts a thorough evaluation"
      },
      {
        "step": "Report Delivery",
        "description": "You receive detailed findings and recommendations"
      }
    ]
  }
]
```

## Implementation Priority

1. **Checklist Component** - Most commonly used, simplest structure
2. **Video Component** - Frequently requested, straightforward implementation
3. **Timeline Component** - Less common but valuable for process-heavy services

## Design System Integration

All components must:
- Use EHS brand colors (navy #003366, gold #FFB81C)
- Follow Maven Pro typography
- Use spacing scale (4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 60px, 80px)
- Be fully responsive
- Match existing service page styling patterns

## Next Steps

1. ✅ Document component requirements (this file)
2. ⏳ Implement meta field registration
3. ⏳ Create meta box UI for managing components
4. ⏳ Build shortcode handlers
5. ⏳ Create render functions
6. ⏳ Add CSS styling
7. ⏳ Integrate into single-services.php template
8. ⏳ Test and document usage
