# DigitalOcean Deployment Setup Notes

## SSH Key Configuration

**Current Status:** The `.env.migration-server` file needs to be updated to use the DigitalOcean-specific SSH key.

**Required Change:**

Update `.env.migration-server`:
```bash
# Change this:
MIGRATION_SSH_KEY=~/.ssh/id_ed25519

# To this:
MIGRATION_SSH_KEY=~/.ssh/id_ed25519_do
```

**Why:** The `id_ed25519_do` key is specifically configured for DigitalOcean server access and is already authorized on the server.

## Verification

After updating the config, test the connection:

```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

If successful, you should connect without a password prompt.

## Documentation

- Complete setup guide: `docs/DIGITALOCEAN_SSH_SETUP.md`
- Server details: `docs/hosting.md`
- Deployment script usage: `README.md` (this directory)
