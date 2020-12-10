<?php

return [
    "name"=> "Saas",
    "title"=> "Multi-tenancy SaaS Module",
    "slug"=> "saas",
    "thumbnail"=> "https://placehold.jp/300x160.png",
    "excerpt"=> "Module for multi-tenancy SaaS product development",
    "description"=> "Module for multi-tenancy SaaS product development",
    "download_link"=> "https://github.com/webreinvent/vaahcms-module-saas/archive/master.zip",
    "author_name"=> "saas",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> true,
    "db_table_prefix"=> "vh_saas_",
    "providers"=> [
        "\\VaahCms\\Modules\\Saas\\Providers\\SaasServiceProvider"
    ],
    "aside-menu-order"=> null,
];
