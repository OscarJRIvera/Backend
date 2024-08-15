# Backend


# Prerequisites
PHP 8+
Composer 2.7.7
Ready for XAMPP 8.2.12

# Steps

Clone repository 
---
Once inside the project directory, install the required PHP dependencies using Composer: composer install
---
configure the following variables based on your local environment setup:
---
![image](https://github.com/user-attachments/assets/a10a0ae4-7752-4d52-8771-d7fe7382ba49)
Need Private key since it uses RSA
---
APP_SECRET: Generate a new secret using php bin/console secret:generate.

Using the current database is an option or can create database schema with the following:
---
php bin/console doctrine:database:create
---
php bin/console doctrine:schema:update --force

If you want to Import Database instead and using XAMPP:
1. Open phpMyAdmin("Admin") and go to the "Import" tab.
2. Select the SQL file (`data.sql`) from this repository.
3. Click "Go" to import the database.

You can run the task manually with the following command : 
---
php bin/console ExtractDataCommand 
---
This command will execute the data extraction process from the API and upload files to DB and with SFTP if configured properly

The composer.json file contains all the necessary dependencies for the project.


Changes php.ini (enabled the following): 
---
1. extension=zip 
2. extension=pdo_mysql
