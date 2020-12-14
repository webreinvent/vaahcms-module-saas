# VaahSaaS - vaahcms-module-saas
> Module for multi-tenancy SaaS product development

---
## Features

### Completed
:white_check_mark: Support for DigitalOcean Database (Requires PHP version => 7.4 & `ssl_ca_path` 
is path of ssl certificate which is also required)

:white_check_mark: CRUD for Database servers | Type: MySql & Cpanel MySql

:white_check_mark: CRUD for Tenant

:white_check_mark: Identify Tenant via Sub-Domain  | `tenant1.example.com` or `tenant2.example.com`

:white_check_mark: Identify Tenant via Path  | `example.com/tenant1` or `example.com/tenant2`



### Planned

:black_square_button: Identify Tenant via Domain  | `example.com`


---

## Usages

### Identify Tenant via `URL` and `middleware`

**- By Path**

Add `tenant_by_path` middleware and `path` parameter is required to identify the `tenant`
```php
Route::group(
[
    'prefix'     => '/<URI>/{path}',
    'middleware' => ['web', 'tenant_by_path'],
    'namespace' => 'Frontend',
],
function () {
    //------------------------------------------------
    Route::get( '/', 'TenantController@index' )
        ->name( 'vh.frontend.tenant' );
    //------------------------------------------------
});
```

**- By Sub-domain**

Add `tenant_by_sub_domain` middleware to identify the `tenant`
```php
Route::group(
    [
        'domain' => '{sub_domain}.domain.com',
        'prefix'     => '/<root-uri-sub-domain>', //this should not conflict with root domain
        'middleware' => ['web', 'tenant_by_sub_domain'],
        'namespace' => 'Frontend',
    ],
    function () {
        //------------------------------------------------
        Route::get( '/', 'TenantController@index' )
            ->name( 'vh.tenant' );
        //------------------------------------------------
    });
```

**- By domain - Pending for Future Release**

Add `tenant_by_domain` middleware to identify the `tenant`
```php
Route::group(
[
    'middleware' => ['web', 'tenant_by_domain'],
    'namespace' => 'Frontend',
],
function () {
    //------------------------------------------------
    Route::get( '/', 'TenantController@index' )
        ->name( 'vh.frontend.tenant' );
    //------------------------------------------------
});
```

### Identify Tenant via `Tenant` model instance

```php
$tenant = Tenant::find($id);

$tenancy = new Tenancy($tenant);

//to connect tenant database
$tenancy->start();

//disconnect to tenant database and reconnect to central database
$tenancy->end();

```

### Tenant `Database` operations

```php
$tenant = Tenant::find(2);

$db_manager = new DatabaseManager();
//$created = $db_manager->createDatabase($tenant);
//$created = $db_manager->deleteDatabase($tenant);
//$created = $db_manager->databaseExists($tenant);

```

### Tenant `migrations` & `seeds` operations
```php
$inputs = [
   'command' => '',
   'path' => '',
];


Tenant::migrate($inputs, $tenant_id);



$inputs = [
   'command' => '',
   'class' => '',
];


Tenant::seed($inputs, $tenant_id);

```

### Share Cache with Sub Domains
Add following to your `.env` file:
```dotenv
SESSION_DOMAIN=.domain.com
```
Replace `domain.com` with you actual domain. Clear cache `php artisan config:cache` so that new setting can be applied.

---

#### For hot reload
APP_MODULE_SAAS_ENV=develop

---

## Setup wildcard sub domains on CPanel for Laravel

Wild Card SSL: https://certifytheweb.com/
 
Other SSL Sources: https://letsencrypt.org/docs/client-options/ 

- Login `CPanel >> Subdomains`, enter `*` in subdomain, in `domain` choose `top level domain`, 
remove `_wildcard_` from `Document Root`.

After this if you visit any subdomain it will show SSL error, to resolve it we need to install
`wildcard ssl`. 
- Visit `https://www.sslforfree.com/` and enter `*.yourdomain.com` and click `Create Free Certificate`
- Add `A` record to domain as per the instruction. On next page it will show all there SSL Certificate strings.
- Login `CPanel >> SSL/TLS >> Manage SSL sites >> Install an SSL Website` and choose `*.yourdomain.com` and enter SSL Certificate string to respective section and save.
- Now try to visit any sub domain all of them should be working.
- Before deploying VaahSaas, create a folder `vaahsaas` and move all files and folder in that except `public`
- Rename `public` folder to `public_html` and change the content to following:
```php
require __DIR__.'/../vaahsaas/vendor/autoload.php';
$app = require_once __DIR__.'/../vaahsaas/bootstrap/app.php';
```
- So in root folder you will have two folder `vaahsaas` and `public_html` which can been deployed to the `root` of cpanel


---
### Xampp - Custom domain 

- Open `<xampp>\apache\conf\extra\httpd-vhosts.conf` add following code:
```shell
<VirtualHost yourdomain.com>
  DocumentRoot "<xampp-path>/htdocs/vaahsaas_director/public"
  ServerName yourdomain.com

  <Directory "<xampp-path>/htdocs/vaahsaas_director/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
  </Directory>
</VirtualHost>

<VirtualHost test.yourdomain.com>
  DocumentRoot "<xampp-path>/htdocs/vaahsaas_director/public"
  ServerName test.yourdomain.com

  <Directory "<xampp-path>/htdocs/vaahsaas_director/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
  </Directory>
</VirtualHost>
```

- Then open `notepad` as administrator `C:\Windows\System32\drivers\etc\host`
- Add following lines:
```shell
127.0.0.1 yourdomain.com
127.0.0.1 test.yourdomain.com
```
- You may see some error`test.yourdomain.com` like `Tenancy Not Identified` if you have already 
not created any tenancy for the domain.

---
## Resource
- https://tenancyforlaravel.com/docs/v3/tenants/

---
## Commands
```shell script
php artisan migrate --path=/VaahCms/Modules/Saas/Database/Migrations

php artisan migrate:rollback  --path=/VaahCms/Modules/Saas/Database/Migrations

php artisan db:seed --class=VaahCms\Modules\Saas\Database\Seeds\SampleDataTableSeeder
```
