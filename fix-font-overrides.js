const mysql = require('mysql2/promise');

// Database configuration for DDEV
const dbConfig = {
  host: '127.0.0.1',
  user: 'db',
  password: 'db',
  database: 'db',
  port: 51398
};

async function fixFontOverrides(targetIds = null) {
  let connection;

  try {
    // Connect to database
    connection = await mysql.createConnection(dbConfig);
    console.log('Connected to database');

    // Get all posts with Elementor data, or specific ones if provided
    let query = `
      SELECT p.ID, p.post_title, p.post_name, p.post_type,
             pm.meta_value as elementor_data
      FROM wp_posts p
      LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_elementor_data'
      WHERE pm.meta_value IS NOT NULL
    `;

    if (targetIds && targetIds.length > 0) {
      query += ` AND p.ID IN (${targetIds.join(',')})`;
    }

    query += ` ORDER BY p.post_type, p.post_title`;

    const [rows] = await connection.execute(query);

    console.log(`Found ${rows.length} posts to process`);

    let totalFixed = 0;
    let totalOverrides = 0;

    for (const row of rows) {
      try {
        const elementorData = JSON.parse(row.elementor_data);

        // Check for font overrides in this page/template
        const result = await fixElementorData(elementorData, row.ID, connection);

        if (result.hasOverrides) {
          console.log(`${row.post_type}: ${row.post_title} (ID: ${row.ID}) - Fixed ${result.fixedCount} overrides`);
          totalFixed += result.fixedCount;
          totalOverrides += result.overrideCount;
        }
      } catch (parseError) {
        console.warn(`Error parsing Elementor data for post ${row.ID} (${row.post_title}):`, parseError.message);
      }
    }

    console.log(`\n=== FONT OVERRIDE FIX RESULTS ===`);
    console.log(`Total overrides found: ${totalOverrides}`);
    console.log(`Total overrides fixed: ${totalFixed}`);
    console.log(`Remaining overrides: ${totalOverrides - totalFixed}`);

  } catch (error) {
    console.error('Database error:', error);
  } finally {
    if (connection) {
      await connection.end();
      console.log('\nDatabase connection closed');
    }
  }
}

async function fixElementorData(elementorData, postId, connection) {
  let fixedCount = 0;
  let overrideCount = 0;
  let hasChanges = false;

  function traverseAndFix(elements) {
    if (!Array.isArray(elements)) return;

    for (const element of elements) {
      // Check if this is a widget with settings
      if (element.elType === 'widget' && element.settings) {
        const widgetResult = fixWidgetTypography(element.settings);
        if (widgetResult.hasOverrides) {
          overrideCount += widgetResult.overrideCount;
          if (widgetResult.fixed) {
            fixedCount += widgetResult.fixedCount;
            hasChanges = true;
          }
        }
      }

      // Recursively check child elements
      if (element.elements) {
        traverseAndFix(element.elements);
      }
    }
  }

  traverseAndFix(elementorData);

  // If we made changes, update the database
  if (hasChanges) {
    try {
      const newData = JSON.stringify(elementorData);
      await connection.execute(
        'UPDATE wp_postmeta SET meta_value = ? WHERE post_id = ? AND meta_key = ?',
        [newData, postId, '_elementor_data']
      );
    } catch (updateError) {
      console.error(`Failed to update post ${postId}:`, updateError.message);
    }
  }

  return { hasOverrides: overrideCount > 0, fixedCount, overrideCount };
}

function fixWidgetTypography(settings) {
  let hasOverrides = false;
  let fixed = false;
  let overrideCount = 0;
  let fixedCount = 0;

  // Check for typography_typography = "custom"
  if (settings.typography_typography === 'custom') {
    hasOverrides = true;
    overrideCount++;

    // Remove the custom typography setting
    delete settings.typography_typography;
    fixed = true;
    fixedCount++;

    // Remove specific font override properties
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
        delete settings[key];
      }
    });

    // Remove responsive variants
    const responsiveKeys = ['typography_font_size_mobile', 'typography_font_size_tablet'];
    responsiveKeys.forEach(key => {
      if (settings[key] !== undefined) {
        delete settings[key];
      }
    });
  }

  return { hasOverrides, fixed, overrideCount, fixedCount };
}

// Check if specific IDs were provided via command line
const targetIds = process.argv.slice(2).map(id => parseInt(id)).filter(id => !isNaN(id));

if (targetIds.length > 0) {
  console.log(`Fixing font overrides for specific posts: ${targetIds.join(', ')}`);
  fixFontOverrides(targetIds);
} else {
  console.log('Fixing font overrides for all Elementor posts...');
  fixFontOverrides();
}