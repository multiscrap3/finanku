DEPLOYMENT_CHECKLIST.md

# Shared Hosting Deployment (cPanel)

1. Upload files ke public_html via FTP/File Manager
2. Create subdomain & set document root ke /public folder
3. Create MySQL database & user via cPanel
4. **ASSIGN user ke database dengan ALL PRIVILEGES** ← PENTING!
5. Edit .env dengan exact credentials dari cPanel
6. Run: php artisan migrate --force
7. Run: php artisan db:seed --force
8. Set .env: APP_DEBUG=false, APP_ENV=production
9. Clear bootstrap/cache files
10. Test akses domain
