# Add SSH Key via DigitalOcean Console

## Quick Steps

1. **Open Droplet Console:**
   - Go to: https://cloud.digitalocean.com/droplets/544676631
   - Click **"Access"** → **"Launch Droplet Console"**

2. **In the console, run these commands:**

```bash
# Create .ssh directory if it doesn't exist
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Add the SSH public key
echo 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOFP+uPl7VE6fZ97F48ctvhyF5t2xKwKBU/GJcxfztpl elliotnoteware@Elliots-MacBook-Pro.local' >> ~/.ssh/authorized_keys

# Set correct permissions
chmod 600 ~/.ssh/authorized_keys

# Verify the key was added
cat ~/.ssh/authorized_keys
```

3. **Test from your local machine:**

```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

You should connect without a password prompt.

4. **Test deployment:**

```bash
cd migration-scripts
./quick-deploy-to-do.sh
```

## Alternative: Use DigitalOcean Settings

Instead of console, you can also:

1. Go to droplet settings: https://cloud.digitalocean.com/droplets/544676631/settings
2. Click **"SSH Keys"** tab
3. Ensure **"elliot-macbook-do"** (ID: 53407380) is enabled
4. The key will be automatically added to the server

## Verification

After adding the key, run:

```bash
cd migration-scripts
./add-ssh-key-to-server.sh
```

This will test the connection and confirm the key is working.
