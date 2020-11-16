# vaahcms-module-saas
Saas Module for VaahCMS


#### To Run Modules Dusk Test:
- Change path of dusk in `phpunit.dusk.xml` to following:
```xml
...
<directory suffix="Test.php">./VaahCms/Modules/Saas/Tests/Browser</directory>
...
```

- Then run `php artisan dusk`