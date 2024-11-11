<?php namespace VaahCms\Modules\Saas\Libraries\DatabaseManagers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;
use Illuminate\Support\Facades\Config;
use VaahCms\Modules\Saas\Models\ServerV3;

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
    public function __construct(ServerV3 $server, Tenant $tenant=null)
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


        if(
            isset($this->server->password)
            && !empty($this->server->password)
            && $this->server->password != ""
        )
        {
            $config['password'] = Crypt::decrypt($this->server->password);
        }


        if(!empty($this->server->sslmode))
        {
            if(isset($this->server->meta->ssl_key_path)
                && !empty($this->server->meta->ssl_key_path))
            {
                $config['options']['PDO::MYSQL_ATTR_SSL_KEY'] = $this->server->meta->ssl_key_path;
            }

            if(isset($this->server->meta->ssl_cert_path)
                && !empty($this->server->meta->ssl_cert_path))
            {
                $config['options']['PDO::MYSQL_ATTR_SSL_CERT'] = $this->server->meta->ssl_cert_path;
            }



            if(isset($this->server->meta->ssl_ca_path)
                && !empty($this->server->meta->ssl_ca_path))
            {
                $config['options']['PDO::MYSQL_ATTR_SSL_CA'] = $this->server->meta->ssl_ca_path;
            }

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


        if(isset($this->tenant->database_password) && !empty($this->tenant->database_password))
        {
            $config['password'] = Crypt::decrypt($this->tenant->database_password);
        }

        if(!is_null($this->tenant->database_sslmode) && $this->tenant->database_sslmode != 'disable')
        {
            if(isset($this->tenant->meta->ssl_key_path)
                && empty($this->tenant->meta->ssl_key_path))
            {
                $config['options']['PDO::MYSQL_ATTR_SSL_KEY'] = $this->tenant->meta->ssl_key_path;
            }

            if(isset($this->tenant->meta->ssl_cert_path)
                && empty($this->tenant->meta->ssl_cert_path))
            {
                $config['options']['PDO::MYSQL_ATTR_SSL_CERT'] = $this->tenant->meta->ssl_cert_path;
            }

            if(isset($this->tenant->meta->ssl_ca_path)
                && empty($this->tenant->meta->ssl_ca_path))
            {
                $config['options']['PDO::MYSQL_ATTR_SSL_CA'] = $this->tenant->meta->ssl_ca_path;
            }

        }

        $this->tenant_config = $config;
        $this->tenant_connection_name = $this->tenant->db_connection_name;

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
        $charset = $this->tenant->database_charset;
        $collation = $this->tenant->database_collation;

        try{

            $this->connectToServer();
            $sql = "CREATE DATABASE  IF NOT EXISTS `{$database}` CHARACTER SET `$charset` COLLATE `$collation`";
            $this->server_connection
                ->statement($sql);

            $this->grantAllPrivileges($this->server_config['username']);

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

            $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '";
            $sql .= $this->tenant->database_name."'";

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
    public function createDatabaseUser()
    {

        try{



            $this->connectToServer();

            $this->deleteDatabaseUser();

            $sql = "CREATE USER '".$this->tenant_config['username'];
            $sql .= "'@'".$this->server->host."' IDENTIFIED BY '";
            $sql .= $this->tenant_config['password']."'";

            $this->server_connection->statement($sql);

            $response['status'] = 'success';
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

            $this->connectToServer();

            $sql = "GRANT ALL PRIVILEGES ON ".$this->tenant->database_name;
            $sql .= ".* TO '".$this->tenant->database_username."'@'".$this->server->host."'";

            $this->server_connection->statement($sql);

            $response['status'] = 'success';

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


            $this->grantAllPrivileges($this->server_config['username']);

            $this->connectToServer();

            $sql = "DROP USER '".$this->tenant->database_username;
            $sql .= "'@'".$this->server->host."'";

            $this->server_connection->statement($sql);

            $this->flushPrivileges();

            $response['status'] = 'success';
            $response['messages'][] = 'Database User Deleted';

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }

        return $response;
    }
    //--------------------------------------------------------

    public function grantAllPrivileges($username)
    {
        try{

            $this->connectToServer();

            $sql = "GRANT ALL PRIVILEGES ON ";
            $sql .= "*.* TO '".$username."'@'".$this->server->host."'";

            $this->server_connection->statement($sql);

            $response['status'] = 'success';

            $response['messages'][] = 'Database access assigned to the user';

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }
    }

    //--------------------------------------------------------
    public function flushPrivileges()
    {
        try{

            $this->connectToServer();

            $sql = "FLUSH PRIVILEGES";

            $this->server_connection->statement($sql);

            $response['status'] = 'success';
            $response['messages'][] = 'Privileges flushed';

        }catch(\Exception $e)
        {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();

        }
    }
    //--------------------------------------------------------
    //--------------------------------------------------------

}
