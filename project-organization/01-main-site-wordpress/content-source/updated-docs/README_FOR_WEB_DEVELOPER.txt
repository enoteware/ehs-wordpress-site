═══════════════════════════════════════════════════════════════
JSON FILES FOR WEB DEVELOPER - README
EHS Analytical Solutions Website Updates
December 2025
═══════════════════════════════════════════════════════════════

WHAT'S IN THIS FOLDER:

Individual JSON files for each page:
1. 1_Web_Designer_Instructions_Part1_SSHO.json
2. 2_Web_Designer_Instructions_Part2_LeadCompliance.json
3. 3_Part3_Caltrans_Construction_Safety_NEW.json
4. 4_Web_Designer_Instructions_Part4_Federal_Contracting.json
5. 5_Web_Designer_Instructions_Part5_Global_Updates.json

Master file with all pages:
- ALL_PAGES_MASTER.json (contains all 5 pages in one file)

═══════════════════════════════════════════════════════════════

JSON STRUCTURE EXPLAINED:

Each page JSON contains:

{
  "pageInfo": {
    "filename": "original_filename.txt",
    "title": "Page Title",
    "description": "Page Description"
  },
  
  "technical": {
    "url": "/page-url/",
    "pageTitle": "SEO Page Title (for <title> tag)",
    "metaDescription": "SEO meta description (for <meta> tag)",
    "h1Heading": "Main H1 heading for page",
    "keywords": ["keyword1", "keyword2", "keyword3"]
  },
  
  "navigation": {
    "placement": "Where to place in menu",
    "menuText": "Text to display in menu"
  },
  
  "sections": [
    {
      "sectionName": "HERO SECTION",
      "type": "hero",
      "elements": [
        {
          "type": "heading1",
          "text": "Heading text"
        },
        {
          "type": "text",
          "text": "Paragraph text"
        },
        {
          "type": "button",
          "text": "Button text"
        },
        {
          "type": "image",
          "description": "Image recommendation"
        },
        {
          "type": "list",
          "items": ["item1", "item2", "item3"]
        }
      ]
    }
  ]
}

═══════════════════════════════════════════════════════════════

ELEMENT TYPES YOU'LL SEE:

- "heading1", "heading2", "heading3" = H1, H2, H3 tags
- "text" = Regular paragraph text
- "label" = Subheading or label
- "button" = CTA button
- "image" = Image with description/alt text recommendation
- "list" = Bulleted or checkmark list

═══════════════════════════════════════════════════════════════

HOW TO USE THESE FILES:

OPTION 1: Parse JSON Programmatically
- Read the JSON file in your preferred language (JavaScript, PHP, Python, etc.)
- Loop through sections and elements
- Generate HTML based on element types

OPTION 2: Use Master File
- ALL_PAGES_MASTER.json contains all 5 pages
- Parse once and access all page data
- Useful for bulk processing

OPTION 3: Manual Reference
- Open JSON files in text editor
- Copy content for each section
- Paste into your CMS or HTML templates

═══════════════════════════════════════════════════════════════

EXAMPLE: Reading JSON in JavaScript

```javascript
fetch('1_Web_Designer_Instructions_Part1_SSHO.json')
  .then(response => response.json())
  .then(data => {
    // Set page title
    document.title = data.technical.pageTitle;
    
    // Set meta description
    const meta = document.querySelector('meta[name="description"]');
    meta.content = data.technical.metaDescription;
    
    // Loop through sections
    data.sections.forEach(section => {
      console.log('Section:', section.sectionName);
      console.log('Type:', section.type);
      
      // Loop through elements in each section
      section.elements.forEach(element => {
        if (element.type === 'heading1') {
          // Create H1
          const h1 = document.createElement('h1');
          h1.textContent = element.text;
          document.body.appendChild(h1);
        }
        else if (element.type === 'text') {
          // Create paragraph
          const p = document.createElement('p');
          p.textContent = element.text;
          document.body.appendChild(p);
        }
        else if (element.type === 'list') {
          // Create list
          const ul = document.createElement('ul');
          element.items.forEach(item => {
            const li = document.createElement('li');
            li.textContent = item;
            ul.appendChild(li);
          });
          document.body.appendChild(ul);
        }
        // ... handle other element types
      });
    });
  });
```

═══════════════════════════════════════════════════════════════

EXAMPLE: Reading JSON in PHP

```php
<?php
$json = file_get_contents('1_Web_Designer_Instructions_Part1_SSHO.json');
$data = json_decode($json, true);

// Set page title
$pageTitle = $data['technical']['pageTitle'];

// Set meta description
$metaDescription = $data['technical']['metaDescription'];

// Loop through sections
foreach ($data['sections'] as $section) {
    echo "<div class='section' data-type='{$section['type']}'>";
    echo "<h2>{$section['sectionName']}</h2>";
    
    foreach ($section['elements'] as $element) {
        switch ($element['type']) {
            case 'heading1':
                echo "<h1>{$element['text']}</h1>";
                break;
            case 'text':
                echo "<p>{$element['text']}</p>";
                break;
            case 'list':
                echo "<ul>";
                foreach ($element['items'] as $item) {
                    echo "<li>{$item}</li>";
                }
                echo "</ul>";
                break;
            // ... handle other types
        }
    }
    
    echo "</div>";
}
?>
```

═══════════════════════════════════════════════════════════════

IMPLEMENTATION PRIORITY:

WEEK 1: SSHO Services Page (Highest Priority)
File: 1_Web_Designer_Instructions_Part1_SSHO.json
URL: /ssho-services-california/

WEEK 2: Lead Compliance Plan Services Page
File: 2_Web_Designer_Instructions_Part2_LeadCompliance.json
URL: /lead-compliance-plan-services/

WEEK 3: Caltrans Construction Safety Page
File: 3_Part3_Caltrans_Construction_Safety_NEW.json
URL: /caltrans-construction-safety-services/

WEEK 4: Federal Contracting + Global Updates
Files: 4 & 5
URLs: Multiple pages and updates

═══════════════════════════════════════════════════════════════

NOTE ABOUT ORIGINAL FILES:

If you prefer the original Word documents and text files (non-JSON),
they are still available. The JSON files are an alternative format
that some web developers find easier to work with programmatically.

Both formats contain the same content - use whichever works best
for your workflow!

═══════════════════════════════════════════════════════════════

QUESTIONS?

Contact Adam at EHS Analytical Solutions:
- Phone: (619) 288-3094
- Email: adam@ehsanalytical.com

═══════════════════════════════════════════════════════════════
END OF README
═══════════════════════════════════════════════════════════════
