# Run Sync Script Manually

## Why Run Manually?

The automated script needs an interactive terminal to prompt for your SSH password. The server is also currently rate-limiting connections.

## Steps to Run

1. **Open a terminal** (not in Cursor's automated environment)

2. **Navigate to the directory**:
   ```bash
   cd /Users/elliotnoteware/code/ehs/ehs-wordpress-local
   ```

3. **Run the sync script**:
   ```bash
   ./sync-elementor-templates.sh
   ```

4. **Enter password when prompted**:
   - The script will prompt for your SSH password
   - Enter the password for `a96c427e_1@832f87585d.nxcli.net`
   - You may be prompted multiple times (for different SSH/SCP operations)

## What the Script Does

1. Tests SSH connection (prompts for password)
2. Finds header/footer templates in production
3. Exports templates with all Elementor data
4. Downloads templates to local
5. Imports templates into local DDEV environment

## If Rate Limit Still Active

If you see "Too many authentication failures":
- Wait 15-20 minutes
- Then try again

## Alternative: Test Connection First

Test the connection manually first:
```bash
ssh ehs-prod
```

If this works (you can connect and enter password), then the sync script will work too.
