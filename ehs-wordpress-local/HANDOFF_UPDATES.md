# Elementor Template Sync - Handoff Updates

## Changes Made

### 1. SSH Configuration Fixed

**Problem**: SSH authentication was failing with "too many authentication failures" because the script was trying multiple keys.

**Solution**:
- Added SSH config entry in `~/.ssh/config` for `ehs-prod` host
- Updated sync script to use SSH config alias
- Improved error handling and connection testing

**SSH Config Added**:
```
Host ehs-prod
    HostName 832f87585d.nxcli.net
    User a96c427e_1
    Port 22
    PreferredAuthentications password
    PubkeyAuthentication no
    ServerAliveInterval 60
    ServerAliveCountMax 3
    StrictHostKeyChecking no
```

### 2. Sync Script Improvements

**Updated `sync-elementor-templates.sh`**:
- Now uses SSH config alias (`ehs-prod`) by default
- Better connection testing before attempting operations
- Improved error messages with troubleshooting hints
- Removed `IdentitiesOnly` flag that was causing key conflicts
- Better handling of password authentication (allows interactive prompts)

### 3. New Diagnostic Tools

**Created `test-ssh-connection.sh`**:
- Tests SSH config alias connection
- Tests direct connection
- Tests WP-CLI access
- Provides troubleshooting guidance

**Usage**:
```bash
cd ehs-wordpress-local
./test-ssh-connection.sh
```

### 4. Documentation Updates

**Updated `ELEMENTOR_TEMPLATE_SYNC.md`**:
- Added prerequisites section
- Added SSH troubleshooting section
- Updated with new connection method

**Created `SSH_SETUP.md`**:
- Complete SSH configuration guide
- Authentication method options (password vs key)
- Troubleshooting common issues
- Step-by-step key setup instructions

## Current Status

### ✅ What's Working
- SSH config entry added and configured
- Sync script updated to use SSH config
- Test script available for diagnostics
- Documentation updated

### ⚠️ Next Steps

**Option 1: Test Current Setup (Password Auth)**
```bash
cd ehs-wordpress-local
./test-ssh-connection.sh
```

If password authentication works, you can run the sync script interactively:
```bash
./sync-elementor-templates.sh
```
(You'll be prompted for password)

**Option 2: Set Up SSH Key (Recommended for Automation)**
Follow instructions in `SSH_SETUP.md` to set up SSH key authentication for passwordless access.

**Option 3: Manual Export (If SSH Still Fails)**
Use the manual method documented in `QUICK_EXPORT_GUIDE.md`:
1. SSH into production manually
2. Export templates using WP-CLI
3. Download files to local
4. Run import script

## Files Modified/Created

### Modified
- `ehs-wordpress-local/sync-elementor-templates.sh` - Updated SSH handling
- `ehs-wordpress-local/ELEMENTOR_TEMPLATE_SYNC.md` - Added troubleshooting

### Created
- `ehs-wordpress-local/test-ssh-connection.sh` - SSH diagnostic tool
- `ehs-wordpress-local/SSH_SETUP.md` - SSH configuration guide
- `ehs-wordpress-local/HANDOFF_UPDATES.md` - This file

### SSH Config
- `~/.ssh/config` - Added `ehs-prod` host entry

## Testing

1. **Test SSH Connection**:
   ```bash
   cd ehs-wordpress-local
   ./test-ssh-connection.sh
   ```

2. **Test Manual SSH**:
   ```bash
   ssh ehs-prod
   ```

3. **Run Sync Script** (if SSH works):
   ```bash
   cd ehs-wordpress-local
   ./sync-elementor-templates.sh
   ```

## Notes

- The SSH config uses password authentication by default (matching other Nexcess hosts)
- For automated scripts, consider setting up SSH key authentication
- The sync script will prompt for password if needed (interactive mode)
- All scripts now use the SSH config alias for consistency
