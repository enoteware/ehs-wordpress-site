# Service Pages to Services Post Type - Quick Start Guide

This guide provides a quick overview of the steps to migrate your existing WordPress service pages to the new Services Custom Post Type.

## 🚀 Quick Start Steps

1.  **Start DDEV**
    ```bash
    cd ehs-wordpress-local
    ddev start
    ```

2.  **Identify Service Pages**
    Access the identification script in your browser (requires admin login):
    ```
    https://ehs-local.ddev.site/identify-service-pages.php
    ```
    - Review the report.
    - Copy the "Recommended Migration List" slugs.

3.  **Update Migration Script**
    Open `ehs-wordpress-local/migrate-service-pages.php` and paste the copied slugs into the `$service_page_slugs` array.

4.  **Test Migration (Single Page)**
    - In `migrate-service-pages.php`, comment out all but *one* slug for initial testing.
    - Run the migration script:
      ```
      https://ehs-local.ddev.site/migrate-service-pages.php
      ```
    - Go to WordPress Admin -> Services. Find the new draft service post.
    - Fill in all meta fields (Category, Area, Certifications, etc.).
    - Publish the service.
    - Verify the URL (`https://ehs-local.ddev.site/your-service-slug/`) works and the layout is correct.

5.  **Flush Rewrite Rules**
    Go to WordPress Admin -> Settings -> Permalinks and click "Save Changes". This is crucial for URLs to work.

6.  **Migrate All Remaining Services**
    - Uncomment all slugs in `ehs-wordpress-local/migrate-service-pages.php`.
    - Run the migration script again.
    - Review and publish each new service in WordPress Admin, filling in all meta fields.

7.  **Update Navigation Menus**
    Go to WordPress Admin -> Appearance -> Menus. Replace old page links with the new Services post links.

8.  **Final Verification & Cleanup**
    - Test all migrated service URLs.
    - Confirm responsive design.
    - Delete the old page posts (now in draft status with "-old" suffix).

## 📚 Further Reading

- **`ehs-wordpress-local/SERVICE_MIGRATION_README.md`** for detailed instructions, troubleshooting, and CSS/function reference.
- **`ehs-wordpress-local/IMPLEMENTATION_SUMMARY.md`** for a summary of implemented changes.
