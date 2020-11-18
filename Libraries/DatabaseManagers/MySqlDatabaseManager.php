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
    protected $server_connection;

    //--------------------------------------------------------
    public function __construct(Server $server, Tenant $tenant=null)
    {
        $this->server = $server;

        if($tenant)
        {
            $this->tenant = $tenant;
        }

        $this->setConnectionConfig();

    }

    //--------------------------------------------------------
    protected function getConfig()
    {
        $config = [
            'driver' => $this->server->driver,
            'host' => $this->server->host,
            'port' => $this->server->port,
            'username' => $this->server->username,
            'password' => '',
            'database' => '',
        ];

        if(isset($this->server->password) && !empty($this->server->password))
        {
            $config['password'] = Crypt::decrypt($this->server->password);
        }

        return $config;

    }
    //--------------------------------------------------------
    public function testConnection()
    {
        $config = $this->getConfig();

        Config::set('database.connections.'.$this->server->slug, $config);

        try{
            DB::connection($this->server->slug);
            $response['status'] = 'success';
            $response['messages'][] = 'Successfully connected with the database host.';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
        }

        return $response;

    }
    //--------------------------------------------------------
    protected function setConnectionConfig()
    {
        $config = $this->getConfig();

        Config::set('database.connections.'.$this->server->slug, $config);

        $this->server_connection = DB::connection($this->server->slug);

    }
    //--------------------------------------------------------
    public function createDatabase()
    {
        $database = $this->tenant->database_name;
        $charset = $this->tenant->database_charset;
        $collation = $this->tenant->database_collation;

        try{
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
