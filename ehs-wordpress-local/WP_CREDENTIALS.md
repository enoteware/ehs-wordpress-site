# WordPress Admin Credentials for Local Development

These credentials are configured for the local DDEV environment and are available as environment variables.

## Admin User Credentials

- **Username**: `a509f58b_admin`
- **Password**: `EHS-Local-Dev-2024!`
- **Email**: `enoteware@gmail.com`
- **Site URL**: `https://ehs-local.ddev.site`

## Environment Variables

These are automatically available in the DDEV web container via `web_environment` in `.ddev/config.yaml`:

- `WP_ADMIN_USERNAME` - WordPress admin username
- `WP_ADMIN_PASSWORD` - WordPress admin password
- `WP_ADMIN_EMAIL` - WordPress admin email

## Usage

Access these in scripts or commands:
```bash
# In DDEV container
echo $WP_ADMIN_USERNAME
echo $WP_ADMIN_PASSWORD

# Via WP-CLI
ddev wp user list
```

## Login URL

- **Admin Login**: `https://ehs-local.ddev.site/wp-admin`
- **WP-CLI**: `ddev wp user list`

## Security Note

These credentials are for **local development only** and should never be committed to production or shared publicly.

## Local Development Configuration

### Disabled Plugins

- **iThemes Security Pro** - Automatically disabled in local development via DDEV hooks to prevent PHP 8.2 compatibility issues and deprecation warnings. The plugin is deactivated on every `ddev start` via the `post-start` hook in `.ddev/config.yaml`.
