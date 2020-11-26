# VaahSaaS - vaahcms-module-saas
> Module for multi-tenancy SaaS product development

---
## Features

### Completed
:white_check_mark: Support for DigitalOcean Database (Requires PHP version => 7.4 & `ssl_ca_path` 
is path of ssl certificate which is also required)

### Planned
:black_square_button: CRUD for Database servers | Type: MySql & Cpanel MySql

:black_square_button: CRUD for Tenant

:black_square_button: Identify Tenant via Domain  | `example.com`

:black_square_button: Identify Tenant via Sub-Domain  | `tenant.example.com`

:black_square_button: Identify Tenant via Path  | `example.com/tenant`

:black_square_button: Identify Tenant via Domain  | `example.com/?tenant_slug=tenant`


---

#### For hot reload
APP_MODULE_SAAS_ENV=develop

```php

$tenant = Tenant::find(2);

$db_manager = new DatabaseManager();
//$created = $db_manager->createDatabase($tenant);
//$created = $db_manager->deleteDatabase($tenant);
//$created = $db_manager->databaseExists($tenant);

```


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
