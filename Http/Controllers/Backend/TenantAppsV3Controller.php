<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Entities\TenantApp;
use VaahCms\Modules\Saas\Models\TenantAppV3;
use VaahCms\Modules\Saas\Models\TenantV3;


class TenantAppsV3Controller extends Controller
{


    //----------------------------------------------------------
    public function __construct()
    {

    }

    //----------------------------------------------------------

    public function getAssets(Request $request)
    {

        try{

            $data = [];

            $data['permission'] = [];
            $data['rows'] = config('vaahcms.per_page');

            $data['fillable']['columns'] = TenantAppV3::getFillableColumns();
            $data['fillable']['except'] = TenantAppV3::getUnFillableColumns();
            $data['empty_item'] = TenantAppV3::getEmptyItem();
            $data['search_by'] = [
                ['name'=>'Tenant','value'=>'tenant'],
                ['name'=>'App','value'=>'app']
            ];

            $data['actions'] = [];

            $response['success'] = true;
            $response['data'] = $data;

        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
        }

        return $response;
    }

    //----------------------------------------------------------
    public function getList(Request $request)
    {
        try{
            return TenantAppV3::getList($request);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function updateList(Request $request)
    {
        try{
            return TenantAppV3::updateList($request);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");

            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function listAction(Request $request, $type)
    {


        try{
            return TenantAppV3::listAction($request, $type);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;

        }
    }
    //----------------------------------------------------------
    public function deleteList(Request $request)
    {
        try{
            return TenantAppV3::deleteList($request);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function fillItem(Request $request)
    {
        try{
            return TenantAppV3::fillItem($request);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function createItem(Request $request)
    {
        try{
            return TenantAppV3::createItem($request);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {
        try{
            return TenantAppV3::getItem($id);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function updateItem(Request $request,$id)
    {
        try{
            return TenantAppV3::updateItem($request,$id);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function deleteItem(Request $request,$id)
    {
        try{
            return TenantAppV3::deleteItem($request,$id);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------
    public function itemAction(Request $request,$id,$action)
    {
        try{
            return TenantAppV3::itemAction($request,$id,$action);
        }catch (\Exception $e){
            $response = [];
            $response['success'] = false;
            if(env('APP_DEBUG')){
                $response['errors'][] = $e->getMessage();
                $response['hint'] = $e->getTrace();
            } else{
                $response['errors'][] = trans("vaahcms-general.something_went_wrong");
            }
            return $response;
        }
    }
    //----------------------------------------------------------

    public function databaseActions(Request $request, $id, $action)
    {
        $response = [];

        $response['status'] = 'success';

        $inputs = $request->all();

        $tenant_app = TenantAppV3::find($id);

        if(!$tenant_app)
        {
            $response['status'] = 'failed';
            $response['errors'][] = "Tenant's App does not exist";
            return response()->json($response);
        }

        $request->request->set('inputs', $tenant_app->vh_saas_tenant_id);

        $request->request->set('path', $tenant_app->app->migration_path);


        switch ($action)
        {

            //------------------------------------
            case 'create':

                $response = TenantV3::createDatabase($tenant_app->vh_saas_tenant_id);

                break;
            //------------------------------------
            case 'delete':

                $response = TenantV3::deleteDatabase($tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {
                    $update = [
                        'last_migrated_at' => null,
                        'last_seeded_at' => null,
                        'is_active' => null,
                    ];

                    //set for all the apps of the tenant
                    TenantAppV3::where('vh_saas_tenant_id', $tenant_app->vh_saas_tenant_id)
                        ->update($update);
                }

                break;
            //------------------------------------
            case 'create-user':

                $response = TenantV3::createDatabaseUser($tenant_app->vh_saas_tenant_id);

                break;
            //------------------------------------
            case 'assign-user':

                $response = TenantV3::assignUserToDatabase($tenant_app->vh_saas_tenant_id);

                break;
            //------------------------------------
            case 'delete-user':

                $response = TenantV3::deleteDatabaseUser($tenant_app->vh_saas_tenant_id);

                break;
            //------------------------------------
            case 'migrate':
                $request->request->set('command', 'migrate');
                $response = TenantV3::migrate($request->all(), $tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {
                    $tenant_app->last_migrated_at = \Carbon::now();
                    $tenant_app->save();
                }

                break;
            //------------------------------------
            case 'rollback':

                $request->request->set('command', 'migrate:rollback');
                $response = TenantV3::migrate($request->all(), $tenant_app->vh_saas_tenant_id);

                break;

            //------------------------------------
            case 'seed':
                $request->request->set('class', $tenant_app->app->seed_class);
                $request->request->set('command', 'db:seed');
                $response = TenantV3::seed($request->all(), $tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {
                    $tenant_app->last_seeded_at = \Carbon::now();
                    $tenant_app->save();
                }

                break;
            //------------------------------------
            case 'insert-sample-data':
                $request->request->set('class', $tenant_app->app->sample_data_class);
                $request->request->set('command', 'db:seed');
                $response = TenantV3::seed($request->all(), $tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {
                    $tenant_app->last_seeded_at = \Carbon::now();
                    $tenant_app->save();
                }

                break;
            //------------------------------------
            case 'wipe':
                $request->request->set('command', 'db:wipe');
                $response = TenantV3::seed($request->all(), $tenant_app->vh_saas_tenant_id);
                if($response['status'] == 'success')
                {
                    $update = [
                        'last_migrated_at' => null,
                        'last_seeded_at' => null,
                        'is_active' => null,
                    ];

                    //set for all the apps of the tenant
                    TenantAppV3::where('vh_saas_tenant_id', $tenant_app->vh_saas_tenant_id)
                        ->update($update);
                }

                break;
            //------------------------------------
            //------------------------------------

        }

        return response()->json($response);

    }

}
