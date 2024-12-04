# LarryFreeMap Deployment Guide

This guide will walk you through deploying the LarryFreeMap application to AWS Lightsail.

## Prerequisites
- AWS Account
- Local development environment
- SSH client (built into macOS/Linux, PuTTY for Windows)
- FileZilla (optional) for FTP file transfer

## Deployment Steps

### 1. Prepare Project Files
```bash
# Make the setup script executable before zipping
chmod +x project/setup.sh

# Create zip archive of the project
zip -r project.zip project/
```

### 2. AWS Lightsail Setup
1. Navigate to [lightsail.aws.amazon.com](https://lightsail.aws.amazon.com)
2. Create a new instance with these specifications:
   - Platform: Linux/Unix
   - Blueprint: LAMP (PHP 8)
   - Network: Dual-stack
   - Plan: $5 USD per month
   - Name: Choose a meaningful instance name
   - Instance count: 1
3. Click "Create instance"

### 3. Instance Management
1. From the Instances dashboard, click the 3 dots menu on your instance card
2. Select "Manage"
3. Note down the Public IPv4 Address for later use
4. Click "Connect using SSH" to access the browser-based SSH console

### 4. File Transfer
Using SCP (macOS/Linux):
```bash
# If needed, adjust permissions for the key file
chmod 600 /path/to/your/key.pem

# Transfer the project archive
scp -i /path/to/your/key.pem project.zip bitnami@<Your_Instance_IP>:/home/bitnami/
```

Alternatively, use FileZilla to transfer the files using SFTP.

### 5. Server Setup
Connect to your instance via SSH and run these commands:

```bash
# SSH into your server (if not using browser-based SSH)
ssh bitnami@<Your_Instance_IP>

# Navigate to home directory
cd /home/bitnami

# Unzip the project
unzip project.zip

# Make the script executable again (in case permissions were lost)
chmod +x project/setup.sh

# Move to the project directory
cd project

# Run the setup script
sudo ./setup.sh
```

The setup script will automatically:
- Use the correct Bitnami paths and user/group (daemon:daemon)
- Set up the project in /opt/bitnami/apache2/htdocs/project
- Configure Apache using Bitnami's virtual host configuration
- Set proper permissions
- Create and configure the .env file with your Supabase credentials
- Restart Apache using Bitnami's control script

### 6. Verify Installation
1. Open your web browser
2. Navigate to `http://<Your_Instance_IP>/`
3. You should see the login page if everything is set up correctly

## Troubleshooting
- If you can't connect via SCP, verify the key file permissions (chmod 600)
- If the website doesn't load:
  - Check the Apache error logs: `tail -f /opt/bitnami/apache2/logs/project-error.log`
  - Verify the setup script executed successfully
  - Ensure all file permissions are correct
  - Check Apache status: `sudo /opt/bitnami/ctlscript.sh status apache`
- If you need to restart Apache:
  - Use: `sudo /opt/bitnami/ctlscript.sh restart apache`
- To check Apache configuration:
  - Use: `sudo /opt/bitnami/apache2/bin/apachectl -t`

## Security Notes
- Remember to secure your .env file with proper credentials
- Regularly update your instance for security patches
- Consider setting up SSL/TLS for HTTPS
- Review Apache access logs periodically: `tail -f /opt/bitnami/apache2/logs/access_log`
