<?php namespace VaahCms\Modules\Saas\Libraries\DatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;
use Illuminate\Support\Facades\Config;
use WebReinvent\CPanel\CPanel;

class CpanelMySqlDatabaseManager
{

    protected $tenant;
    protected $server;
    protected $cpanel_config;
    protected $server_connection_name;
    protected $cpanel;
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
            'cpanel_domain' => $this->server->meta->cpanel_domain,
            'cpanel_username' => $this->server->meta->cpanel_username,
            'protocol' => $this->server->meta->protocol,
            'port' => $this->server->meta->port,
            'cpanel_api_token' => '',
        ];

        if(isset($this->server->meta->cpanel_api_token)
            && !empty($this->server->meta->cpanel_api_token))
        {
            $config['cpanel_api_token'] = Crypt::decrypt($this->server->meta->cpanel_api_token);
        }

        $this->cpanel_config = $config;


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

        if(isset($this->tenant->database_password) && !empty($this->tenant->database_password))
        {
            $config['password'] = Crypt::decrypt($this->tenant->database_password);
        }

        $this->tenant_config = $config;

        $this->tenant_connection_name = $this->tenant->db_connection_name;;

    }
    //--------------------------------------------------------
    public function setDatabaseConnectionName($name, $config)
    {
        Config::set('database.connections.'.$name, $config);
    }
    //--------------------------------------------------------
    public function testServerConnection()
    {

        $this->connectToCpanel();

        try{

            $module = "Mysql";
            $function = "list_databases";
            $parameters = [];

            $response = $this->cpanel->callUAPI($module, $function, $parameters);
            $response['messages'][] = 'Successfully connected with the database server';

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
        }

        return $response;

    }
    //--------------------------------------------------------
    public function connectToCpanel()
    {


        $cpanel = new CPanel(
            $this->cpanel_config['cpanel_domain'],
            $this->cpanel_config['cpanel_api_token'],
            $this->cpanel_config['cpanel_username'],
            $this->cpanel_config['protocol'],
            $this->cpanel_config['port']
        );

        $this->cpanel = $cpanel;

    }

    //--------------------------------------------------------
    public function connectToServer()
    {


        try{

            $this->connectToCpanel();

            $response['status'] = 'success';
            $response['data']['connection_name'] = $this->server_connection_name;
            $response['data']['config'] = $this->cpanel;

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }


        return $response;
    }
    //--------------------------------------------------------
    public function configDbConnection()
    {

        try{
            $this->setDatabaseConnectionName($this->tenant_connection_name, $this->tenant_config);
            //$this->tenant_connection = DB::connection($this->tenant_connection_name);
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

        try{

            $this->connectToCpanel();
            $response = $this->cpanel->createDatabase($database);

            if($response['status'] == 'failed')
            {
                return $response;
            }

            $response['messages'][] = 'Database Created';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------
    public function createDatabaseUser()
    {

        try{

            $this->connectToCpanel();
            $response = $this->cpanel->createDatabaseUser(
                $this->tenant_config['username'],
                $this->tenant_config['password']
            );

            if($response['status'] == 'failed')
            {
                return $response;
            }

            $response['messages'][] = 'Database User Created';

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------
    public function assignUserToDatabase()
    {

        try{

            $this->connectToCpanel();
            $response = $this->cpanel->setAllPrivilegesOnDatabase(
                $this->tenant_config['username'],
                $this->tenant_config['database']
            );

            if($response['status'] == 'failed')
            {
                return $response;
            }

            $response['messages'][] = 'Database access assigned to the user';

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------
    public function deleteDatabaseUser()
    {

        try{

            $this->connectToCpanel();
            $response = $this->cpanel->deleteDatabaseUser(
                $this->tenant_config['username']
            );

            if($response['status'] == 'failed')
            {
                return $response;
            }

            $response['messages'][] = 'Database User Deleted';

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
            $this->connectToCpanel();
            $response = $this->cpanel->deleteDatabase(
                $this->tenant_config['username']
            );

            if($response['status'] == 'failed')
            {
                return $response;
            }

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
        $response = [];
        try{
            $this->connectToCpanel();
            $response = $this->cpanel->listDatabases();

            if($response['status'] == 'failed')
            {
                return $response;
            }

            if($response['status'] == 'success')
            {
                if(isset($response['data']->data))
                {
                    foreach($response['data']->data as $db_obj)
                    {
                        if($db_obj->database == $this->tenant_config['database'])
                        {
                            $response['status'] = 'success';
                            $response['messages'][] = 'Database Already Exist';
                            return $response;
                        }
                    }
                }

            }

            $response['status'] = 'failed';
            $response['errors'] = ['Database does not exist'];


        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }

    //--------------------------------------------------------

}
