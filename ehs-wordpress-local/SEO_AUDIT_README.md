# SEO Fields Audit and Auto-Fill

This tool audits all custom post types (services, credentials, clients, team) and automatically fills missing SEO fields with optimized content.

## What It Does

The script checks and generates:

1. **Excerpts** - SEO-friendly post excerpts (150-160 characters)
2. **Yoast SEO Meta Titles** - Optimized titles (max 60 characters)
3. **Yoast SEO Meta Descriptions** - Compelling descriptions (120-160 characters)
4. **Yoast SEO Focus Keywords** - Primary keywords for each post
5. **Open Graph Titles** - Social media sharing titles
6. **Open Graph Descriptions** - Social media sharing descriptions
7. **Featured Image Alt Text** - Accessibility and SEO alt text

## Usage

### Dry Run (Preview Changes)

```bash
cd ehs-wordpress-local
./audit-seo.sh
```

This shows what would be updated without making any changes.

### Apply Changes

```bash
cd ehs-wordpress-local
./audit-seo.sh --apply
```

This actually updates all the SEO fields.

### Direct WP-CLI Usage

```bash
# Dry run
ddev exec wp eval-file audit-and-fill-seo-fields.php --path=/var/www/html/wordpress

# Apply changes
ddev exec "APPLY_CHANGES=1 wp eval-file audit-and-fill-seo-fields.php --path=/var/www/html/wordpress"
```

## How It Works

### Services Posts
- Uses `service_short_description` meta field if available
- Generates location-aware keywords (adds "california" to focus keywords)
- Creates service-specific descriptions

### Credentials Posts
- Uses `credential_acronym` and `credential_issuing_organization` meta fields
- Formats as "Title (Acronym) | Organization"
- Focuses on certification context

### Clients Posts
- Uses `client_industry` and `client_location` meta fields
- Creates client relationship descriptions
- Industry-specific context

### Team Posts
- Uses `_ehs_team_job_title` and `_ehs_team_certifications` meta fields
- Professional bio-style descriptions
- Job title and credential context

## SEO Best Practices Applied

- **Meta Titles**: 50-60 characters (optimal for search results)
- **Meta Descriptions**: 120-160 characters (optimal click-through length)
- **Focus Keywords**: 2-4 words, location-aware for services
- **Excerpts**: 150-160 characters, compelling and informative
- **Alt Text**: Descriptive, includes post title and brand name

## Output Example

```
📊 SUMMARY
============================================================
Total posts audited: 25
Posts updated: 25

Updates by post type:
  • services: 14/14 posts
  • credentials: 9/9 posts
  • clients: 2/2 posts
  • team: 0/0 posts

Updates by field:
  • excerpt: 16 posts
  • yoast_title: 20 posts
  • yoast_desc: 16 posts
  • yoast_focuskw: 16 posts
  • og_title: 25 posts
  • og_desc: 25 posts
```

## Notes

- The script only updates **missing** fields - it won't overwrite existing SEO data
- All generated content is optimized for search engines and follows Yoast SEO best practices
- Location context (California) is automatically added to service post keywords
- Featured image alt text is generated from post titles

## Re-running

You can safely run this script multiple times. It will only update fields that are currently empty or too short.

## Troubleshooting

If Yoast SEO plugin is not active, the script will skip Yoast-specific fields but still generate excerpts and alt text.
