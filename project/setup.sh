#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Starting setup...${NC}"

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

# Set variables for Bitnami LAMP stack
APACHE_USER="daemon"
APACHE_GROUP="daemon"
PROJECT_PATH="/opt/bitnami/apache2/htdocs/project"

# Create project directory
echo -e "${YELLOW}Creating project directory...${NC}"
mkdir -p "$PROJECT_PATH"

# Copy files to web directory
echo -e "${YELLOW}Copying project files...${NC}"
cp -R ./* "$PROJECT_PATH/"
rm -f "$PROJECT_PATH/setup.sh"

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
chown -R $APACHE_USER:$APACHE_GROUP "$PROJECT_PATH"
find "$PROJECT_PATH" -type f -exec chmod 644 {} \;
find "$PROJECT_PATH" -type d -exec chmod 755 {} \;

# Setup environment file
echo -e "${YELLOW}Setting up environment variables...${NC}"
if [ ! -f "$PROJECT_PATH/.env" ]; then
    cp "$PROJECT_PATH/.env.example" "$PROJECT_PATH/.env"
    
    # Prompt for Supabase environment variables
    read -p "Enter Supabase Database Host (e.g., your-ref.pooler.supabase.com): " db_host
    read -p "Enter Supabase Database User (e.g., postgres.your-ref): " db_user
    read -p "Enter Supabase Database Password: " db_password
    read -p "Enter Supabase Project URL (e.g., https://your-ref.supabase.co): " supabase_url
    read -p "Enter Supabase Anon Key: " supabase_anon_key
    
    # Update .env file
    sed -i "s|your-project-ref.pooler.supabase.com|$db_host|" "$PROJECT_PATH/.env"
    sed -i "s|postgres.your-project-ref|$db_user|" "$PROJECT_PATH/.env"
    sed -i "s|your-db-password|$db_password|" "$PROJECT_PATH/.env"
    sed -i "s|https://your-project-ref.supabase.co|$supabase_url|" "$PROJECT_PATH/.env"
    sed -i "s|your-anon-key|$supabase_anon_key|" "$PROJECT_PATH/.env"
fi

# Configure Apache virtual host for Bitnami
echo -e "${YELLOW}Configuring Apache...${NC}"
cat > "/opt/bitnami/apache2/conf/vhosts/project-vhost.conf" << EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "$PROJECT_PATH/public"
    ServerName localhost

    <Directory "$PROJECT_PATH/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "/opt/bitnami/apache2/logs/project-error.log"
    CustomLog "/opt/bitnami/apache2/logs/project-access.log" combined
</VirtualHost>
EOF

# Restart Apache using Bitnami's script
echo -e "${YELLOW}Restarting Apache...${NC}"
/opt/bitnami/ctlscript.sh restart apache

echo -e "${GREEN}Setup complete!${NC}"
echo -e "Your application is now available at: http://your-server-ip"
echo -e "Please ensure to:"
echo -e "1. Configure your DNS if needed"
echo -e "2. Set up SSL/HTTPS if needed"
echo -e "3. Check the Apache error logs if you encounter any issues:"
echo -e "   tail -f /opt/bitnami/apache2/logs/project-error.log"
