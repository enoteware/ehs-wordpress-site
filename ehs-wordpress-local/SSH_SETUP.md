# SSH Configuration for Elementor Template Sync

## SSH Config Entry

An SSH config entry has been added to `~/.ssh/config` for the production server:

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

## Authentication Methods

### Option 1: Password Authentication (Current Setup)

The SSH config is currently set to use password authentication. This means:
- You'll be prompted for a password when connecting
- Automated scripts will need to be run interactively
- Works immediately without additional setup

**To use**: Just run `ssh ehs-prod` and enter your password when prompted.

### Option 2: SSH Key Authentication (Recommended for Automation)

For passwordless automated syncs, set up SSH key authentication:

1. **Generate SSH key** (if you don't have one):
   ```bash
   ssh-keygen -t ed25519 -f ~/.ssh/id_ed25519_ehs_prod
   ```

2. **Copy public key to production**:
   ```bash
   ssh-copy-id -i ~/.ssh/id_ed25519_ehs_prod.pub ehs-prod
   ```
   Or manually:
   ```bash
   cat ~/.ssh/id_ed25519_ehs_prod.pub | ssh ehs-prod "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
   ```

3. **Update SSH config** to use the key:
   ```
   Host ehs-prod
       HostName 832f87585d.nxcli.net
       User a96c427e_1
       IdentityFile ~/.ssh/id_ed25519_ehs_prod
       AddKeysToAgent yes
       UseKeychain yes
   ```

4. **Test connection**:
   ```bash
   ssh ehs-prod
   ```
   Should connect without password prompt.

## Testing Connection

Use the test script to diagnose connection issues:

```bash
cd ehs-wordpress-local
./test-ssh-connection.sh
```

This will:
- Test SSH config alias
- Test direct connection
- Test WP-CLI access
- Provide troubleshooting guidance

## Troubleshooting

### "Too many authentication failures"

This happens when SSH tries multiple keys and the server rejects them. Solutions:

1. **Use SSH config alias** (already set up):
   ```bash
   ssh ehs-prod
   ```

2. **Specify exact key**:
   ```bash
   ssh -i ~/.ssh/id_ed25519_ehs_prod ehs-prod
   ```

3. **Disable key authentication** (if using password):
   ```bash
   ssh -o PubkeyAuthentication=no ehs-prod
   ```

### Connection Timeout

- Check if server is accessible: `ping 832f87585d.nxcli.net`
- Verify firewall/network settings
- Try different port if needed

### Permission Denied

- Verify username is correct: `a96c427e_1`
- Check if password is correct
- Verify SSH key is authorized on server
- Check server logs: `/var/log/auth.log` (on server)
