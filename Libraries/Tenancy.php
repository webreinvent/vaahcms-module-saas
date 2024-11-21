<?php namespace VaahCms\Modules\Saas\Libraries;

use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\DatabaseManager;

class Tenancy
{

    public $tenant;
    public $server;
    public $db_manager;

    public $initialized = false;

    function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function start()
    {
        $this->server = $this->tenant->server;

        $db_manager = new DatabaseManager($this->server, $this->tenant);
        $this->db_manager = $db_manager;

        //set database name to default
        return $db_manager->connectToTenant();

    }



    public function end($connection_name='mysql')
    {
        //set database name to default
        $this->db_manager->connectToCentral($connection_name);
    }






}
