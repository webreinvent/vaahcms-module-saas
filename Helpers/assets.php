<?php

function saas_db_ssl_modes()
{
    $list = [
        'disable',
        'prefer',
        'require'
    ];

    return $list;
}

//-------------------------------------------
function saas_host_types()
{
    $list = [
        'MySql',
        'CPanel-MySql',
        'DigitalOcean-MySql',
    ];

    return $list;
}
//-------------------------------------------
//-------------------------------------------
//-------------------------------------------
