# Database Import Issue - Local DDEV Environment

## Problem Summary

The production database was imported, but WordPress is not recognizing the content even though it exists in the database.

## Current Status

### Database Has Content ✅
- **417 published posts** total in database
- **18 published pages** in database  
- **121 attachments** in database
- **1,559 upload files** in filesystem
- **6 users** in database

### WordPress Not Recognizing Content ❌
- WP-CLI shows only **1-2 pages** (should be 18)
- WP-CLI shows **0 attachments** (should be 121)
- Admin Media Library shows **empty** (should have 121 items)
- Pages return **404** when accessed directly
- All content dates from **2019-2020** (before WordPress install date of 2021-12-17)

## Files Present

### Uploads Folder
- Location: `wordpress/wp-content/uploads/`
- Files: 1,559 files across years 2019-2026
- Direct URL access: ✅ Working (HTTP 200)
- WordPress recognition: ❌ Not working

### Database Content
- Table: `wpoq_posts`
- Pages: 18 published (IDs: 2, 12, 92, 107, 115, 633, 653, 655, 657, 659, 661, 663, 1198, 1200, 2165, 2344, 2493, 2546)
- Attachments: 121 with `post_status='inherit'`
- All updated to `post_author=1` (admin user)

## Attempted Fixes

1. ✅ Updated page authors to user ID 1
2. ✅ Updated attachment authors to user ID 1  
3. ✅ Updated post_modified dates
4. ✅ Flushed rewrite rules
5. ✅ Flushed all caches
6. ❌ Still not working

## Root Cause Hypothesis

WordPress queries are filtering out content that predates the WordPress installation date (2021-12-17). This could be:

1. **Plugin filtering** - A plugin may be filtering queries
2. **WordPress query filter** - Core WordPress may have a filter excluding old content
3. **Database import issue** - Content imported but WordPress metadata not properly set
4. **Date-based filtering** - Some query is filtering by date range

## Next Steps

### Option 1: Re-import Database
```bash
cd /Users/elliotnoteware/code/ehs/ehs-wordpress-local
ddev import-db --file=exports/production-database.sql
```

### Option 2: Check for Query Filters
- Check active plugins that might filter queries
- Check WordPress query filters in theme/plugins
- Check if there's a date-based filter

### Option 3: Force WordPress to Recognize Content
- Update all post dates to be after installation
- Re-register all posts programmatically
- Use WP-CLI to import content properly

## Database Details

- **Table Prefix**: `wpoq_`
- **Database**: `db` (DDEV default)
- **Import File**: `exports/production-database.sql` (126MB, imported Jan 13 15:19)
- **WordPress Version**: 6.9
- **PHP Version**: 8.2

## Commands to Verify

```bash
# Check database content
ddev wp db query "SELECT COUNT(*) FROM wpoq_posts WHERE post_type='page' AND post_status='publish'"

# Check WordPress recognition
ddev wp post list --post_type=page --format=count

# Check attachments
ddev wp db query "SELECT COUNT(*) FROM wpoq_posts WHERE post_type='attachment'"
ddev wp post list --post_type=attachment --format=count
```
