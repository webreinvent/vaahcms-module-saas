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
    protected $server_connection;
    protected $cpanel;
    protected $cpanel_database_name;

    //--------------------------------------------------------
    public function __construct(Server $server, Tenant $tenant=null)
    {
        $this->server = $server;

        if($tenant)
        {
            $this->tenant = $tenant;
        }

        $this->setServerConnection();
        $this->setCPanelConfig();

        $this->cpanel_database_name = $this->cpanel->username.'_'.$this->tenant->database_name;;

    }

    //--------------------------------------------------------
    protected function getServerConfig()
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
    protected function setCPanelConfig()
    {


        $cpanel = new CPanel(
            $this->server->cpanel_domain,
            $this->server->cpanel_api_token,
            $this->server->cpanel_username,
            $this->server->protocol,
            $this->server->port
        );

        $this->cpanel = $cpanel;

    }
    //--------------------------------------------------------
    public function testServerConnection()
    {
        $config = $this->getServerConfig();

        Config::set('database.connections.'.$this->server->slug, $config);

        try{
            DB::connection($this->server->slug);
            $response['status'] = 'success';
        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
        }

        return $response;
    }
    //--------------------------------------------------------
    protected function setServerConnection()
    {
        $config = $this->getServerConfig();

        Config::set('database.connections.'.$this->server->slug, $config);

        $this->server_connection = DB::connection($this->server->slug);

    }
    //--------------------------------------------------------
    public function createDatabase()
    {

        try{

            $response = $this->cpanel->createDatabase($this->cpanel_database_name);

            if($response['status'] == 1)
            {
                $response['status'] = 'success';
                $response['data'] = [];

            } else{
                $response['status'] = 'failed';
            }


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


            $response = $this->cpanel->deleteDatabase($this->cpanel_database_name);

            if($response['status'] == 1)
            {
                $response['status'] = 'success';
                $response['data'] = [];

            } else{
                $response['status'] = 'failed';
            }


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

            $response = $this->cpanel->listDatabases();
            if($response['status'] == 1)
            {

                if(isset($response['data']['data']))
                {

                    foreach ($response['data']['data'] as $database_obj)
                    {
                        if($database_obj->database == $this->cpanel_database_name)
                        {
                            $response['status'] = 'success';
                            $response['data'] = [];
                            return $response;
                        }

                    }

                }

            }


            $response['status'] = 'failed';

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

            //$response = $this->cpanel->createDatabaseUser($this->tenant->, $password);

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------

    //--------------------------------------------------------


    //--------------------------------------------------------
    public function setDefaultConnection(string $connection)
    {
        $this->app['config']['database.default'] = $connection;
        $this->database->setDefaultConnection($connection);
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
