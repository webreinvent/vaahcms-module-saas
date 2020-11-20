<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Entities\TenantApp;


class TenantAppsController extends Controller
{

    public $theme;

    //----------------------------------------------------------
    public function __construct()
    {
        $this->theme = vh_get_backend_theme();
    }

    //----------------------------------------------------------

    public function getAssets(Request $request)
    {

        $data = [];
        $data['permission'] = [];

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);
    }
    //----------------------------------------------------------

    //----------------------------------------------------------
    public function postCreate(Request $request)
    {
        $response = TenantApp::createItem($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getList(Request $request)
    {
        $response = TenantApp::getList($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {
        $response = TenantApp::getItem($id);
        return response()->json($response);
    }

    //----------------------------------------------------------
    public function postStore(Request $request,$id)
    {
        $response = TenantApp::postStore($request,$id);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function postActions(Request $request, $action)
    {
        $rules = array(
            'inputs' => 'required',
        );

        $validator = \Validator::make( $request->all(), $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return response()->json($response);
        }

        $response = [];

        $response['status'] = 'success';

        $inputs = $request->all();

        switch ($action)
        {

            //------------------------------------
            case 'sync-tenant-apps':

                $response = TenantApp::syncTenantApps($request);

                break;
            //------------------------------------
            case 'bulk-change-status':

                $response = TenantApp::bulkStatusChange($request);

                break;
            //------------------------------------
            case 'bulk-trash':

                $response = TenantApp::bulkTrash($request);

                break;
            //------------------------------------
            case 'bulk-restore':

                $response = TenantApp::bulkRestore($request);

                break;

            //------------------------------------
            case 'bulk-delete':

                $response = TenantApp::bulkDelete($request);

                break;
            //------------------------------------
            //------------------------------------

        }

        return response()->json($response);

    }
    //----------------------------------------------------------
    public function databaseActions(Request $request, $id, $action)
    {


        $response = [];

        $response['status'] = 'success';

        $inputs = $request->all();

        $tenant_app = TenantApp::find($id);

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

                $response = Tenant::createDatabase($tenant_app->vh_saas_tenant_id);

                break;
            //------------------------------------
            case 'delete':

                $response = Tenant::deleteDatabase($tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {

                    $update = [
                        'last_migrated_at' => null,
                        'last_seeded_at' => null,
                        'is_active' => null,
                    ];

                    //set for all the apps of the tenant
                    TenantApp::where('vh_saas_tenant_id', $tenant_app->vh_saas_tenant_id)
                        ->update($update);

                }

                break;
            //------------------------------------
            case 'migrate':
                $request->request->set('command', 'migrate');
                $response = Tenant::migrate($request->all(), $tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {
                    $tenant_app->last_migrated_at = \Carbon::now();
                    $tenant_app->save();
                }

                break;
            //------------------------------------
            case 'rollback':

                $request->request->set('command', 'migrate:rollback');
                $response = Tenant::migrate($request->all(), $tenant_app->vh_saas_tenant_id);

                break;

            //------------------------------------
            case 'seed':
                $request->request->set('class', $tenant_app->app->seed_class);
                $request->request->set('command', 'db:seed');
                $response = Tenant::seed($request->all(), $tenant_app->vh_saas_tenant_id);

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
                $response = Tenant::seed($request->all(), $tenant_app->vh_saas_tenant_id);

                if($response['status'] == 'success')
                {
                    $tenant_app->last_seeded_at = \Carbon::now();
                    $tenant_app->save();
                }

                break;
            //------------------------------------
            case 'wipe':
                $request->request->set('command', 'db:wipe');
                $response = Tenant::seed($request->all(), $tenant_app->vh_saas_tenant_id);
                if($response['status'] == 'success')
                {
                    $update = [
                        'last_migrated_at' => null,
                        'last_seeded_at' => null,
                        'is_active' => null,
                    ];

                    //set for all the apps of the tenant
                    TenantApp::where('vh_saas_tenant_id', $tenant_app->vh_saas_tenant_id)
                        ->update($update);
                }

                break;
            //------------------------------------
            //------------------------------------

        }

        return response()->json($response);

    }
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------


}
