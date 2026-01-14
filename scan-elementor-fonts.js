const mysql = require('mysql2/promise');

// Database configuration for DDEV
const dbConfig = {
  host: '127.0.0.1',
  user: 'db',
  password: 'db',
  database: 'db',
  port: 51398
};

async function scanElementorFonts(targetIds = null) {
  let connection;

  try {
    // Connect to database
    connection = await mysql.createConnection(dbConfig);
    console.log('Connected to database');

    // Query for all posts with Elementor data, or specific ones if provided
    let query = `
      SELECT p.ID, p.post_title, p.post_name, p.post_type,
             pm.meta_value as elementor_data
      FROM wp_posts p
      LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_elementor_data'
      WHERE pm.meta_value IS NOT NULL
    `;

    if (targetIds && targetIds.length > 0) {
      query += ` AND p.ID IN (${targetIds.join(',')})`;
    } else {
      query += ` AND p.post_status = 'publish'`;
    }

    query += ` ORDER BY p.post_type, p.post_title`;

    const [rows] = await connection.execute(query);

    console.log(`Found ${rows.length} posts with Elementor data`);

    const pagesWithOverrides = [];
    const templatesWithOverrides = [];

    for (const row of rows) {
      try {
        const elementorData = JSON.parse(row.elementor_data);

        // Check for font overrides in this page/template
        const hasOverrides = checkForFontOverrides(elementorData);

        if (hasOverrides && hasOverrides.length > 0) {
          const item = {
            id: row.ID,
            title: row.post_title,
            slug: row.post_name,
            type: row.post_type,
            overrides: hasOverrides
          };

          if (row.post_type === 'page') {
            pagesWithOverrides.push(item);
          } else {
            templatesWithOverrides.push(item);
          }
        }
      } catch (parseError) {
        console.warn(`Error parsing Elementor data for post ${row.ID} (${row.post_title}):`, parseError.message);
      }
    }

    // Generate report
    console.log('\n=== ELEMENTOR FONT OVERRIDE SCAN RESULTS ===\n');

    console.log(`Pages with font overrides: ${pagesWithOverrides.length}`);
    console.log(`Templates with font overrides: ${templatesWithOverrides.length}`);
    console.log(`Total items needing updates: ${pagesWithOverrides.length + templatesWithOverrides.length}`);

    if (pagesWithOverrides.length > 0) {
      console.log('\n--- PAGES NEEDING UPDATES ---');
      pagesWithOverrides.forEach(page => {
        console.log(`\n${page.title} (ID: ${page.id}, Slug: ${page.slug})`);
        console.log(`  Overrides found: ${page.overrides.length}`);
        page.overrides.forEach(override => {
          console.log(`    - ${override.widgetType}: ${override.fontProperties.join(', ')}`);
        });
      });
    }

    if (templatesWithOverrides.length > 0) {
      console.log('\n--- TEMPLATES NEEDING UPDATES ---');
      templatesWithOverrides.forEach(template => {
        console.log(`\n${template.title} (ID: ${template.id}, Type: ${template.type})`);
        console.log(`  Overrides found: ${template.overrides.length}`);
        template.overrides.forEach(override => {
          console.log(`    - ${override.widgetType}: ${override.fontProperties.join(', ')}`);
        });
      });
    }

    if (pagesWithOverrides.length === 0 && templatesWithOverrides.length === 0) {
      console.log('\n✅ No font overrides found! All pages use global fonts.');
    }

  } catch (error) {
    console.error('Database error:', error);
  } finally {
    if (connection) {
      await connection.end();
      console.log('\nDatabase connection closed');
    }
  }
}

function checkForFontOverrides(elementorData) {
  const overrides = [];

  function traverseElements(elements) {
    if (!Array.isArray(elements)) return;

    for (const element of elements) {
      // Check if this is a widget with settings
      if (element.elType === 'widget' && element.settings) {
        const widgetOverrides = checkWidgetForOverrides(element.settings, element.widgetType);
        if (widgetOverrides && widgetOverrides.length > 0) {
          overrides.push({
            widgetType: element.widgetType,
            elementId: element.id,
            fontProperties: widgetOverrides
          });
        }
      }

      // Recursively check child elements
      if (element.elements) {
        traverseElements(element.elements);
      }
    }
  }

  traverseElements(elementorData);
  return overrides;
}

function checkWidgetForOverrides(settings, widgetType) {
  const fontProperties = [];

  // Check for typography_typography = "custom"
  if (settings.typography_typography === 'custom') {
    fontProperties.push('typography_typography');

    // Check for specific font properties that are overridden
    const fontKeys = [
      'typography_font_family',
      'typography_font_size',
      'typography_font_weight',
      'typography_text_transform',
      'typography_font_style',
      'typography_text_decoration',
      'typography_line_height',
      'typography_letter_spacing'
    ];

    fontKeys.forEach(key => {
      if (settings[key] !== undefined) {
        fontProperties.push(key.replace('typography_', ''));
      }
    });

    // Check responsive variants
    const responsiveKeys = ['typography_font_size_mobile', 'typography_font_size_tablet'];
    responsiveKeys.forEach(key => {
      if (settings[key] !== undefined) {
        fontProperties.push(key.replace('typography_', ''));
      }
    });
  }

  return fontProperties.length > 0 ? fontProperties : null;
}

// Check if specific IDs were provided via command line
const targetIds = process.argv.slice(2).map(id => parseInt(id)).filter(id => !isNaN(id));

// Run the scan
scanElementorFonts(targetIds.length > 0 ? targetIds : null).catch(console.error);