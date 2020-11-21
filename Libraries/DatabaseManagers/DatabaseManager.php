<?php namespace VaahCms\Modules\Saas\Libraries\DatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager as BaseDatabaseManager;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;

class DatabaseManager
{

    protected $tenant=null;
    protected $server=null;

    protected $db_manager;

    public function __construct(Server $server, Tenant $tenant=null)
    {
        $this->server = $server;

        if($tenant)
        {
            $this->tenant = $tenant;
        }

        $this->setDBManager();
    }

    //--------------------------------------------------------
    public function setDBManager()
    {
        $manager = null;
        switch ($this->server->host_type)
        {
            case 'MySql':
                $manager = new MySQLDatabaseManager($this->server, $this->tenant);
                break;

            case 'CPanel-MySql':
                $manager = new CpanelMySqlDatabaseManager($this->server, $this->tenant);
                break;

            default:
                $manager = new MySQLDatabaseManager($this->server, $this->tenant);
                break;
        }

        $this->db_manager = $manager;

    }
    //--------------------------------------------------------
    public function createDatabase()
    {
        $response = $this->db_manager->createDatabase();
        return $response;
    }
    //--------------------------------------------------------
    public function deleteDatabase()
    {
        $response = $this->db_manager->deleteDatabase();
        return $response;
    }
    //--------------------------------------------------------
    public function databaseExists()
    {
        $response = $this->db_manager->databaseExists();
        return $response;
    }
    //--------------------------------------------------------
    public function testServerConnection()
    {
        $response = $this->db_manager->testServerConnection();
        return $response;
    }
    //--------------------------------------------------------

    public function connectToDatabase()
    {
        $response = $this->db_manager->connectToDatabase();
        return $response;
    }

    //--------------------------------------------------------
    public function createDatabaseUser()
    {
        $response = $this->db_manager->createDatabaseUser();
        return $response;
    }
    //--------------------------------------------------------
    public function deleteDatabaseUser()
    {
        $response = $this->db_manager->deleteDatabaseUser();
        return $response;
    }
    //--------------------------------------------------------
    public function assignUserToDatabase()
    {
        $response = $this->db_manager->assignUserToDatabase();
        return $response;
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
