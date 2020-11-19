<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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
    //----------------------------------------------------------


}
