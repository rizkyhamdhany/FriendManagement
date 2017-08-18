# Friend Magement API

### Introduction
This is API for Friend Management Application.
Feature list on this API are:
- Connect Friend
- Friend List
- Find Common Friend
- Subscribe a User
- Block a User
- Find all email addresses that can receive updates from an email address.

see API docs https://documenter.getpostman.com/view/1401695/friendmanagement/6n8vqsE#6465dc98-3f81-66ab-bcab-0fa71f10c30a

### Instalation

You need server that installed Apache, PHP > 5, MySql or you can use XAMPP

1. Install Composer (https://getcomposer.org)
2. Clone This Repo
3. Install Composer in Project Local Directory
    ```
    composer install
    ```
4. Setup env file
    ```
    APP_NAME=Laravel
    APP_ENV=local
    APP_KEY=base64:SFv4ZpRzx+XzGv8709HsrwrdAocoBXz6wIx7sDTTe4k=
    APP_DEBUG=true
    APP_LOG_LEVEL=debug
    APP_URL=http://localhost
    
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=friendmanagement
    DB_USERNAME=root
    DB_PASSWORD=
    ```
6. Create MySQL Databse
    ```
    db name : friendmanagement
    ```
5. Migrate Database & Populate Dummy Data
    ```
    php artisan migrate --seed
    ```
6. See all User From Database
    ```
    endpoint : {url}/api/v1/user
    {url} can be replaced by http://localhost/friendmanagement or your server address
    ```
7. Run Testing
    ```
    ./vendor/bin/codecept run api
    ```
  
### Depedency
    * [Laravel 5.4] https://laravel.com/ for php framework 
    * [Codeception] http://codeception.com/ for testing