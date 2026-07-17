# Complete Tutorial: Deploying PHP Personal Blog on BT‑Panel
Environment Requirements: PHP ≥7.4, MySQL 5.7 / 8.0, pdo_mysql extension enabled

## File List Description
- index.php → Blog homepage
- install.php → Installation wizard page

Required supporting files: config.php (auto‑generated after installation), functions.php (core function file)

> ⚠️Note: The two code snippets you provided are only the homepage and installation page. The full source code also requires: backend admin.php, article page article.php, category page category.php, tag page tag.php, login page login.php, and the functions.php function file. Missing files will cause direct errors.

## 1. Create a New Website in BT‑Panel
1. Log in to BT‑Panel →【Websites】→【Add Site】

2. Fill in configuration
- Domain name: Enter your domain name (use server IP if you do not have a domain)
- Root directory: `/www/wwwroot/your‑site‑folder‑name`
- PHP Version: Select PHP7.4 ~ PHP8.1 (avoid 8.2+ to prevent compatibility errors)
- Database: MySQL, set database username and password, record username, password and database name
- Submit to create the website

> ✅Do not use the old MariaDB 10.1 version. MySQL 5.7 / MySQL8.0 is preferred.

## 2. Enable Required PHP Extensions
1. On the left of BT‑Panel, go to【Software Store】→ Locate your selected PHP version →【Settings】→【Install Extensions】

2. Mandatory extensions to install and enable:
- pdo_mysql
- fileinfo (corresponds to mime_content_type in environment check)

3. Disabled function check: Go to【Disabled Functions】
Remove disabled functions: `putenv` , `symlink` (if present)

4. Save and restart the PHP service

## 3. Upload Website Source Code
1. Enter the folder by clicking the website root directory
2. Upload all source code files:
index.php
install.php
functions.php
admin.php
login.php
article.php
category.php
tag.php
…and all other program files

3. Set directory permissions
- Folder permission: `755`
- File permission: `644`
- uploads folder (auto‑created by installer; create manually if missing) set permission to 777 (for image uploads)

> BT‑Panel operation: Select all files → Right‑click → Permissions, set as above

## 4. Access Installation Wizard to Complete Installation
1. Visit in browser: `http://your‑domain‑or‑IP/install.php`

> 403 error on access: Check whether files are uploaded to the correct directory, do not set rewrite rules arbitrarily

2. Step 1: Environment check
- PHP Version ≥7.4 ✅
- PDO‑MySQL Extension ✅
- mime_content_type Function ✅
Pass all checks → Click【Next, Configure Database】

3. Step 2 Fill in database information
- Database Host: `127.0.0.1` (fixed value for local BT‑Panel database)
- Database Username: Database username generated when creating the site in BT‑Panel
- Database Password: Database password
- Database Name: Database name (program will create it automatically if it does not exist)
- Admin Username: Custom backend login account (e.g. admin)
- Admin Password: Custom backend login password

4. Click【Start Installation】
Wait for prompt: Installation succeeded
> 👉Do NOT refresh the page repeatedly!
> After successful installation, `config.php` database configuration file will be generated automatically.

## 5. Verify Website Access
1. Visit homepage: `http://domain/index.php` to open the blog homepage
2. Backend Address: `http://domain/admin.php` Log in with the admin account and password you set

> ⚠️Security Suggestion: After installation, delete the install.php file on your server! Prevent others from reinstalling the system without permission.

## 6. Optional Configuration (HTTPS, Force HTTPS)
The index.php includes the built‑in `force_https()` function for forced HTTPS. SSL certificate configuration is recommended.
1. BT‑Panel → Website →【SSL】
2. Select Let’s Encrypt free certificate, apply for the certificate
3. Enable【Force HTTPS】
4. Save settings

## 7. Common Deployment Errors & Solutions
### Error 1: Blank Page
1. Enable PHP error reporting: PHP Settings → Configuration File, set display_errors=On
2. Most likely missing the `functions.php` file, re‑upload the file

### Error 2: Database Connection Failed
1. Make sure database host is set to `127.0.0.1`, do not use localhost
2. Double‑check that database username, password and database name are fully consistent
3. Check whether config.php is generated successfully

### Error 3: mime_content_type Unavailable in Environment Check
Go to PHP Settings → Install the fileinfo extension, then restart PHP

### Error 4: Cannot Create config.php, Installation Failed
Insufficient website directory permissions. Set website root directory permission to 755, owner group to www

### Error 5: Weather Interface Not Displaying
The Hefeng Weather key in the source code is a sample key with limited call quota. Weather data will fail to load once the limit is exceeded. Apply for your free key on the Hefeng Weather official website and replace the key value inside the JS code.
