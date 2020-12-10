<?php
namespace VaahCms\Modules\Saas\Database\Seeds;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedServers();
        $this->seedTenants();
        $this->seedApps();
    }


    //------------------------------------------------------------
    public function getListFromJson($json_file_name)
    {
        $json_file = __DIR__."/json/".$json_file_name;
        $jsonString = file_get_contents($json_file);
        $list = json_decode($jsonString, true);
        return $list;
    }
    //------------------------------------------------------------
    public function storeSeedsWithUuid($table, $list, $primary_key='slug', $create_slug=true, $create_slug_from='name')
    {
        foreach ($list as $item)
        {
            if($create_slug)
            {
                $item['slug'] = Str::slug($item[$create_slug_from]);
            }

            $item['uuid'] = Str::uuid();

            $record = DB::table($table)
                ->where($primary_key, $item[$primary_key])
                ->first();

            if(isset($item['meta']))
            {
                $item['meta'] = json_encode($item['meta']);
            }

            if(!$record)
            {
                DB::table($table)->insert($item);
            } else{
                DB::table($table)->where($primary_key, $item[$primary_key])
                    ->update($item);
            }
        }
    }
    //------------------------------------------------------------
    public function seedServers()
    {
        $list = $this->getListFromJson("servers.json");
        $this->storeSeedsWithUuid('vh_saas_servers', $list);
    }
    //------------------------------------------------------------
    public function seedTenants()
    {
        $list = $this->getListFromJson("tenants.json");
        $this->storeSeedsWithUuid('vh_saas_tenants', $list);
    }
    //------------------------------------------------------------
    public function seedApps()
    {
        $list = $this->getListFromJson("apps.json");
        $this->storeSeedsWithUuid('vh_saas_apps', $list);
    }
    //------------------------------------------------------------
    //------------------------------------------------------------
    //------------------------------------------------------------


}
