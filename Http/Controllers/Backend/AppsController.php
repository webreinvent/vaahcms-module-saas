<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Saas\Entities\App;


class AppsController extends Controller
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

        $data['app_types'] = [
            'Module',
            'Other'
        ];

        $modules = \VaahModule::getAllNames();
        $modules_list = [];
        $i = 0;
        foreach ($modules as $module)
        {
            $modules_list[$i]['name'] = $module;
            $modules_list[$i]['relative_path'] = \VaahModule::getRelativePath($module);
            $modules_list[$i]['migration_path'] = \VaahModule::getTenantMigrationPath($module);
            $modules_list[$i]['seed_class'] = \VaahModule::getTenantSeedsClass($module);
            $modules_list[$i]['sample_data_class'] = \VaahModule::getTenantSampleData($module);
            $modules_list[$i]['version'] = \VaahModule::getVersion($module);
            $modules_list[$i]['version_number'] = \VaahModule::getVersionNumber($module);

            $i++;
        }



        $data['modules'] = $modules_list;


        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);
    }
    //----------------------------------------------------------

    //----------------------------------------------------------
    public function postCreate(Request $request)
    {
        $response = App::createItem($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getList(Request $request)
    {
        $response = App::getList($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {
        $response = App::getItem($id);
        return response()->json($response);
    }

    //----------------------------------------------------------
    public function postStore(Request $request,$id)
    {
        $response = App::postStore($request,$id);
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
            case 'bulk-change-status':

                $response = App::bulkStatusChange($request);

                break;
            //------------------------------------
            case 'bulk-trash':

                $response = App::bulkTrash($request);

                break;
            //------------------------------------
            case 'bulk-restore':

                $response = App::bulkRestore($request);

                break;

            //------------------------------------
            case 'bulk-delete':

                $response = App::bulkDelete($request);

                break;
            //------------------------------------
            //------------------------------------

        }

        return response()->json($response);

    }
    //----------------------------------------------------------
    public function getItemTenants(Request $request, $id)
    {
        $response = App::getItemTenants($request, $id);
        return response()->json($response);
    }
    //----------------------------------------------------------


}
