<?php

return [
    "name"=> "Saas",
    "title"=> "SaaS - A multi-tenancy SaaS Module",
    "slug"=> "saas",
    "thumbnail"=> "https://img.site/p/300/160",
    "excerpt"=> "Module for multi-tenancy SaaS product development",
    "description"=> "Module for multi-tenancy SaaS product development",
    "download_link"=> "https://github.com/webreinvent/vaahcms-module-saas/archive/master.zip",
    "author_name"=> "saas",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.8",
    "is_dev" => env('APP_MODULE_SAAS_ENV')?true:false,
    "is_migratable"=> true,
    "central_domain"=> env('CENTRAL_DOMAIN'),
    "is_sample_data_available"=> true,
    "db_table_prefix"=> "vh_saas_",
    "providers"=> [
        "\\VaahCms\\Modules\\Saas\\Providers\\SaasServiceProvider"
    ],
    "aside-menu-order"=> null,
];
