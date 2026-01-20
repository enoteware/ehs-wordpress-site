# DigitalOcean SSH Key Setup Guide

This guide explains how to set up SSH key authentication for the DigitalOcean server to enable automated deployments.

## Overview

The project uses a dedicated SSH key (`id_ed25519_do`) for DigitalOcean server access. This key is required for:
- Automated deployments via `quick-deploy-to-do.sh`
- Database migrations
- File synchronization
- Server management scripts

## Prerequisites

- Access to the DigitalOcean server (via password or existing key)
- SSH client installed on your local machine
- Write access to `~/.ssh/` directory

## Step 1: Generate SSH Key (if needed)

If you don't already have the DigitalOcean key:

```bash
# Generate a new ED25519 key pair
ssh-keygen -t ed25519 -f ~/.ssh/id_ed25519_do -C "ehs-digitalocean"

# Set proper permissions
chmod 600 ~/.ssh/id_ed25519_do
chmod 644 ~/.ssh/id_ed25519_do.pub
```

**Note:** If you already have the key, skip to Step 2.

## Step 2: Add Public Key to Server

You need to add your public key to the server's `authorized_keys` file.

### Option A: Using Password Authentication (Initial Setup)

```bash
# 1. Copy your public key
cat ~/.ssh/id_ed25519_do.pub

# 2. SSH into server using password
ssh root@134.199.221.85

# 3. On the server, add the key
mkdir -p ~/.ssh
echo "PASTE_YOUR_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh

# 4. Exit and test
exit
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

### Option B: Using Existing Key

If you have another key that works:

```bash
# Copy public key to server
ssh-copy-id -i ~/.ssh/id_ed25519_do.pub root@134.199.221.85

# Or manually:
cat ~/.ssh/id_ed25519_do.pub | ssh root@134.199.221.85 "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys && chmod 700 ~/.ssh"
```

## Step 3: Configure Local Environment

Update `.env.migration-server` to use the correct key:

```bash
MIGRATION_SSH_KEY=~/.ssh/id_ed25519_do
```

The deployment scripts will automatically expand the `~` to your home directory.

## Step 4: Test Connection

Verify the key works:

```bash
# Test SSH connection
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85

# If successful, you should see the server prompt without password
```

## Step 5: Test Deployment Script

Run the quick deploy script to verify everything works:

```bash
cd migration-scripts
./quick-deploy-to-do.sh
```

If successful, you should see the deployment progress without password prompts.

## Troubleshooting

### "Permission denied (publickey)"

**Cause:** The public key isn't in the server's `authorized_keys` file.

**Solution:**
1. Verify the public key is correct: `cat ~/.ssh/id_ed25519_do.pub`
2. Check server's authorized_keys: `ssh root@134.199.221.85 "cat ~/.ssh/authorized_keys"`
3. Ensure the key is added correctly (one key per line, no extra spaces)

### "Could not open key file"

**Cause:** Key file permissions are incorrect.

**Solution:**
```bash
chmod 600 ~/.ssh/id_ed25519_do
chmod 644 ~/.ssh/id_ed25519_do.pub
```

### "Host key verification failed"

**Cause:** Server's host key changed or isn't in known_hosts.

**Solution:**
```bash
# Remove old entry
ssh-keygen -R 134.199.221.85

# Re-add (will prompt to accept)
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

### Script fails silently

**Cause:** SSH connection fails but script doesn't show error.

**Solution:**
```bash
# Test SSH manually first
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85 "echo 'Connection successful'"

# Run script with debug output
bash -x ./quick-deploy-to-do.sh
```

## SSH Config (Optional)

For easier access, add to `~/.ssh/config`:

```
Host ehs-do
    HostName 134.199.221.85
    User root
    IdentityFile ~/.ssh/id_ed25519_do
    IdentitiesOnly yes
```

Then you can use: `ssh ehs-do`

## Security Best Practices

1. **Use ED25519 keys** - More secure than RSA
2. **Protect private key** - Never share or commit `id_ed25519_do`
3. **Use passphrase** - Consider adding a passphrase when generating the key
4. **Rotate keys** - Periodically rotate SSH keys for security
5. **Limit access** - Only add keys from trusted machines

## Related Documentation

- [Hosting Infrastructure](../docs/hosting.md) - Server details and configuration
- [Migration Scripts README](../migration-scripts/README.md) - Deployment script usage
- [Development Setup](../docs/dev-setup.md) - Local development environment
