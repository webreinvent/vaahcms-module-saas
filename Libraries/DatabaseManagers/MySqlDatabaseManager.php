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
            'database' => '',
            'username' => $this->server->username,
            'password' => '',

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


        try{
            $this->setDatabaseConnectionName($this->server_connection_name, $this->server_config);
            $this->server_connection = DB::connection($this->server_connection_name);

            $response['status'] = 'success';
            $response['data']['connection_name'] = $this->server_connection_name;
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

        try{
            $this->setDatabaseConnectionName($this->tenant_connection_name, $this->tenant_config);
            $this->tenant_connection = DB::connection($this->tenant_connection_name);
            $response['status'] = 'success';
            $response['data']['connection_name'] = $this->tenant_connection_name;
            $response['data']['config'] = $this->tenant_config;

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
            $sql = "CREATE DATABASE `{$database}` CHARACTER SET `$charset` COLLATE `$collation`";
            $this->server_connection
                ->statement($sql);
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
            $this->connectToServer();

            $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$this->tenant->database_name'";

            $this->server_connection->select($sql);

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

}
