# SSH Key Analysis for DigitalOcean

## Summary

**Date:** $(date)
**Droplet:** ehs-wp-migration (134.199.221.85)
**Droplet ID:** 544676631

## DigitalOcean Registered SSH Keys

| ID | Name | Fingerprint | Status |
|----|------|-------------|--------|
| 53407380 | elliot-macbook-do | 1f:90:b4:36:01:19:ea:06:01:2e:b9:50:18:2c:ea:4b | ✅ Matches local key |
| 53329060 | mac-mini-ehs | 71:5c:86:2f:9d:12:a6:60:76:df:81:24:4e:77:32:57 | ❓ Check if matches local |

## Local SSH Keys

### ✅ id_ed25519_do (MATCHES DigitalOcean key)
- **Location:** `~/.ssh/id_ed25519_do`
- **Public Key:** `ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOFP+uPl7VE6fZ97F48ctvhyF5t2xKwKBU/GJcxfztpl elliotnoteware@Elliots-MacBook-Pro.local`
- **Fingerprint:** SHA256:VLFXeTeX5y9exNXgpki12JO8j3/1boAgXjswZ4oTuC4
- **Matches:** DigitalOcean key "elliot-macbook-do" (ID: 53407380)
- **Status:** ✅ Registered in DigitalOcean, but may not be on droplet

### id_ed25519
- **Location:** `~/.ssh/id_ed25519`
- **Public Key:** `ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIA6pMjLTvtW0L7hu2w15VlAfRFpBDM4liui77ZMP6kId ElliotsMacBookPro`
- **Fingerprint:** SHA256:L0hlKTiUb2U/TwqHqeAM3Cr1+lmdiXdrzNn1AUEb7+o
- **Status:** ❓ Not registered in DigitalOcean

### id_mac_mini
- **Location:** `~/.ssh/id_mac_mini`
- **Public Key:** `ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICGgJLR3YAY5o/6/61Z2PO9Cf0nz6/tl86wlgEaxr+vQ macbook-to-mini`
- **Fingerprint:** SHA256:XVNeuSij5YoNHrPFkVmkn+I8kLMWeCLB4wvWIhIo02k
- **Status:** ❓ Check if matches "mac-mini-ehs" key

## Current Configuration

**`.env.migration-server`:**
```bash
MIGRATION_SSH_KEY=~/.ssh/id_ed25519  # ❌ Should be id_ed25519_do
```

**Should be:**
```bash
MIGRATION_SSH_KEY=~/.ssh/id_ed25519_do  # ✅ Matches DigitalOcean key
```

## Issue

The `id_ed25519_do` key is registered in DigitalOcean but SSH connection fails. This means:

1. **The key may not be on the droplet** - Keys registered in DigitalOcean don't automatically get added to existing droplets
2. **The key needs to be added to the droplet** - Either via DigitalOcean console or manually to `authorized_keys`

## Solutions

### Option 1: Add Key to Droplet via DigitalOcean Console (Recommended)

1. Go to DigitalOcean Console: https://cloud.digitalocean.com/droplets/544676631
2. Click "Settings" → "SSH Keys"
3. Add the "elliot-macbook-do" key (ID: 53407380) if not already there
4. The key will be automatically added to `/root/.ssh/authorized_keys` on the server

### Option 2: Add Key Manually via SSH (If you have another way in)

If you can access the server via password or another method:

```bash
# 1. Copy the public key
cat ~/.ssh/id_ed25519_do.pub

# 2. SSH into server (using password or another method)
ssh root@134.199.221.85

# 3. On the server, add the key
mkdir -p ~/.ssh
echo "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOFP+uPl7VE6fZ97F48ctvhyF5t2xKwKBU/GJcxfztpl elliotnoteware@Elliots-MacBook-Pro.local" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh

# 4. Test
exit
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

### Option 3: Use DigitalOcean API to Add Key

```bash
# Check current droplet SSH keys
doctl compute droplet get 544676631 -o json | jq '.ssh_keys'

# Note: You cannot add SSH keys to existing droplets via API
# You must use the console or manually add to authorized_keys
```

## Verification Steps

After adding the key:

1. **Update `.env.migration-server`:**
   ```bash
   MIGRATION_SSH_KEY=~/.ssh/id_ed25519_do
   ```

2. **Test SSH connection:**
   ```bash
   ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
   ```

3. **Test deployment script:**
   ```bash
   cd migration-scripts
   ./quick-deploy-to-do.sh
   ```

## Next Steps

1. ✅ Update `.env.migration-server` to use `id_ed25519_do`
2. ⏳ Add the key to the droplet (via console or manually)
3. ✅ Test SSH connection
4. ✅ Test deployment script

## Related Documentation

- [DigitalOcean SSH Setup Guide](DIGITALOCEAN_SSH_SETUP.md)
- [Hosting Infrastructure](hosting.md)
- [Migration Scripts README](../migration-scripts/README.md)
