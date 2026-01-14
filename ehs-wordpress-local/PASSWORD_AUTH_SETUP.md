# Password Authentication Setup Complete

## Configuration Updated

### SSH Config (`~/.ssh/config`)
The `ehs-prod` host is now configured for **password authentication only**:

```
Host ehs-prod
    HostName 832f87585d.nxcli.net
    User a96c427e_1
    Port 22
    PreferredAuthentications password
    PubkeyAuthentication no
    IdentitiesOnly yes
    ServerAliveInterval 60
    ServerAliveCountMax 3
    StrictHostKeyChecking no
```

### Sync Script Updated
The `sync-elementor-templates.sh` script now:
- Uses password authentication for all SSH/SCP commands
- Disables public key authentication
- Will prompt for password when needed

## Usage

### Test Connection
Wait for rate limit to reset (5-10 minutes), then test:

```bash
ssh ehs-prod
```

You'll be prompted for password. Enter the password when prompted.

### Run Sync Script
Once password authentication works:

```bash
cd ehs-wordpress-local
./sync-elementor-templates.sh
```

The script will prompt for password when needed for:
- SSH connections
- SCP file transfers

## Notes

- Password will be prompted interactively (can't be automated without additional tools)
- All SSH/SCP commands in the sync script now use password auth
- Rate limiting may still be active - wait a few minutes if connection fails
