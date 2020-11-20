<?php namespace VaahCms\Modules\Saas\Libraries\DatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;
use Illuminate\Support\Facades\Config;

class MySqlDatabaseManager
{

    protected $tenant;
    protected $server;
    protected $server_config;
    protected $server_connection_name;
    protected $server_connection;
    protected $tenant_config;
    protected $tenant_connection_name;
    protected $tenant_connection;

    //--------------------------------------------------------
    public function __construct(Server $server, Tenant $tenant=null)
    {
        $this->server = $server;
        $this->setServerConfig();

        if(isset($tenant) && !empty($tenant))
        {
            $this->tenant = $tenant;
            $this->setTenantConfig();
        }

    }

    //--------------------------------------------------------
    protected function setServerConfig()
    {
        $config = [
            'driver' => $this->server->driver,
            'host' => $this->server->host,
            'port' => $this->server->port,
            'username' => $this->server->username,

        ];

        if(isset($this->server->password) && !empty($this->server->password))
        {
            $config['password'] = Crypt::decrypt($this->server->password);
        }

        $this->server_config = $config;
        $this->server_connection_name = $this->server->slug;

    }
    //--------------------------------------------------------
    protected function setTenantConfig()
    {
        $config = [
            'driver' => $this->server->driver,
            'host' => $this->server->host,
            'port' => $this->server->port,
            'database' => $this->tenant->database_name,
            'username' => $this->tenant->database_username,
            'password' => '',
        ];

        if(isset($this->tenant->password) && !empty($this->tenant->password))
        {
            $config['password'] = Crypt::decrypt($this->tenant->password);
        }

        $this->tenant_config = $config;
        $this->tenant_connection_name = $this->server->slug.'-'.$this->tenant->slug;

    }
    //--------------------------------------------------------
    public function setDatabaseConnectionName($name, $config)
    {
        Config::set('database.connections.'.$name, $config);
    }
    //--------------------------------------------------------
    public function testServerConnection()
    {
        
        $this->setDatabaseConnectionName($this->server_connection_name, $this->server_config);

        try{
            DB::connection($this->server_connection_name);
            $response['status'] = 'success';
            $response['messages'][] = 'Successfully connected with the database server';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
        }

        return $response;

    }
    //--------------------------------------------------------

    //--------------------------------------------------------
    public function connectToServer()
    {


        $connection_name = $this->server->slug;

        try{
            Config::set($this->server_connection_name, $this->server_config);
            $this->server_connection = DB::connection($connection_name);
            $response['status'] = 'success';
            $response['data']['connection_name'] = $connection_name;
            $response['data']['config'] = $this->server_config;

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }


        return $response;
    }
    //--------------------------------------------------------
    public function connectToDatabase()
    {
        $connection_name = $this->server->slug;

        try{
            Config::set($this->tenant_connection_name, $this->tenant_config);
            $this->server_connection = DB::connection($connection_name);
            $response['status'] = 'success';
            $response['data']['connection_name'] = $connection_name;
            $response['data']['config'] = $this->config;

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }


        return $response;
    }
    //--------------------------------------------------------
    public function createDatabase()
    {


        $database = $this->tenant->database_name;
        $charset = $this->tenant->database_charset;
        $collation = $this->tenant->database_collation;

        try{
            $this->connectToServer();
            $this->server_connection
                ->statement("CREATE DATABASE `{$database}` CHARACTER SET `$charset` COLLATE `$collation`");
            $response['status'] = 'success';
            $response['messages'][] = 'Database Created';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------
    public function deleteDatabase()
    {

        try{
            $this->connectToServer();
            $this->server_connection
                ->statement("DROP DATABASE `{$this->tenant->database_name}`");
            $response['status'] = 'success';
            $response['messages'][] = 'Database Deleted';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------
    public function databaseExists()
    {
        try{
            $this->connectToDatabase();
            $this->server_connection
                ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->tenant->database_name'");
            $response['status'] = 'success';
            $response['messages'][] = 'Database Already Exist';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }

    //--------------------------------------------------------
    public function setDefaultConnection(string $connection)
    {
        $this->app['config']['database.default'] = $connection;
        $this->database->setDefaultConnection($connection);
    }
    //--------------------------------------------------------

}
