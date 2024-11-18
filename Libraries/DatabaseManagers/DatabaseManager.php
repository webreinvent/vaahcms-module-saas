<?php namespace VaahCms\Modules\Saas\Libraries\DatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager as BaseDatabaseManager;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\CpanelMySqlDatabaseManager;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\MySqlDatabaseManager;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\DoMySqlDatabaseManager;
use App;
use VaahCms\Modules\Saas\Models\ServerV3;
use VaahCms\Modules\Saas\Models\TenantV3;

class DatabaseManager
{

    protected $tenant=null;
    protected $server=null;

    protected $db_manager;

    public function __construct(ServerV3 $server, Tenant $tenant=null)
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

            case 'DigitalOcean-MySql':
                $manager = new DoMySqlDatabaseManager($this->server, $this->tenant);
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

    public function configDbConnection()
    {
        $response = $this->db_manager->configDbConnection();
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
    public function connectToTenant()
    {

        $db_config = $this->configDbConnection();

        if($db_config['status'] == 'failed')
        {
            return $db_config;
        }

        $this->setDefaultConnection($this->tenant->db_connection_name);
    }
    //--------------------------------------------------------

    public function connectToCentral(string $db_connection_name='mysql')
    {
        $this->setDefaultConnection($db_connection_name);
    }

    //--------------------------------------------------------

    public function setDefaultConnection(string $connection_name)
    {
        App::make('config')->set('database.default', $connection_name);
        App::make('config')->set('queue.connections.database.connection', $connection_name);
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
