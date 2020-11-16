<?php namespace VaahCms\Modules\Saas\Libraries\DatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager as BaseDatabaseManager;
use VaahCms\Modules\Saas\Entities\Tenant;

class DatabaseManager
{

    protected $app;

    protected $database;

    protected $config;

    protected $db_manager;

    public function __construct()
    {

    }

    //--------------------------------------------------------
    public function setDBManager(Tenant $tenant)
    {
        $manager = null;
        switch ($tenant->server->hosted_by)
        {
            case 'mysql':
                $manager = new MySQLDatabaseManager($tenant->server, $tenant);
                break;

            case 'cpanel-mysql':
                $manager = new CpanelMySqlDatabaseManager($tenant->server, $tenant);
                break;

            default:
                $manager = new MySQLDatabaseManager($tenant->server, $tenant);
                break;
        }

        $this->db_manager = $manager;

    }
    //--------------------------------------------------------
    public function createDatabase(Tenant $tenant)
    {
        $this->setDBManager($tenant);
        $response = $this->db_manager->createDatabase();
        return $response;
    }
    //--------------------------------------------------------
    public function deleteDatabase(Tenant $tenant)
    {
        $this->setDBManager($tenant);
        $response = $this->db_manager->deleteDatabase();
        return $response;
    }
    //--------------------------------------------------------
    public function databaseExists(Tenant $tenant)
    {
        $this->setDBManager($tenant);
        $response = $this->db_manager->databaseExists();
        return $response;
    }
    //--------------------------------------------------------

    public function connectToTenant(TenantWithDatabase $tenant)
    {
        $this->database->purge('tenant');
        $this->createTenantConnection($tenant);
        $this->setDefaultConnection('tenant');
    }

    //--------------------------------------------------------

    public function reconnectToCentral()
    {
        if (tenancy()->initialized) {
            $this->database->purge('tenant');
        }

        $this->setDefaultConnection($this->config->get('tenancy.database.central_connection'));

    }

    //--------------------------------------------------------

    public function setDefaultConnection(string $connection)
    {
        $this->app['config']['database.default'] = $connection;
        $this->database->setDefaultConnection($connection);
    }

    //--------------------------------------------------------

    public function createTenantConnection(TenantWithDatabase $tenant)
    {
        $this->app['config']['database.connections.tenant'] = $tenant->database()->connection();
    }

    //--------------------------------------------------------
    //--------------------------------------------------------
    //--------------------------------------------------------
    //--------------------------------------------------------
    //--------------------------------------------------------
    //--------------------------------------------------------
    //--------------------------------------------------------
    //--------------------------------------------------------

}
