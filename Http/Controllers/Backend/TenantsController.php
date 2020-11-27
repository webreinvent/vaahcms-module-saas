<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;


class TenantsController extends Controller
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
        $data['database_sslmodes'] = saas_db_ssl_modes();

        $data['bulk_actions'] = vh_general_bulk_actions();

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);
    }
    //----------------------------------------------------------

    //----------------------------------------------------------
    public function postCreate(Request $request)
    {
        $response = Tenant::createItem($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getList(Request $request)
    {
        $response = Tenant::getList($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {
        $response = Tenant::getItem($id);
        return response()->json($response);
    }

    //----------------------------------------------------------
    public function postStore(Request $request,$id)
    {
        $response = Tenant::postStore($request,$id);
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

                $response = Tenant::bulkStatusChange($request);

                break;
            //------------------------------------
            case 'bulk-trash':

                $response = Tenant::bulkTrash($request);

                break;
            //------------------------------------
            case 'bulk-restore':

                $response = Tenant::bulkRestore($request);

                break;

            //------------------------------------
            case 'bulk-delete':

                $response = Tenant::bulkDelete($request);

                break;
            //------------------------------------
            case 'create-database':

                $response = Tenant::createDatabase($request);

                break;
            //------------------------------------
            case 'delete-database':

                $response = Tenant::deleteDatabase($request);

                break;
            //------------------------------------

        }

        return response()->json($response);

    }
    //----------------------------------------------------------
    public function postMigrate(Request $request, $uuid)
    {

        $inputs = $request->all();

        $response = Tenant::migrate($inputs, $uuid);

        return response()->json($response);

    }
    //----------------------------------------------------------
    public function getServers(Request $request)
    {
        $rules = array(
            'q' => 'required',
        );

        $validator = \Validator::make( $request->all(), $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return response()->json($response);
        }

        $data = [];

        $list = Server::where(function ($q) use($request){
            $q->where('name', 'LIKE', '%'.$request->q.'%')
            ->orWhere('slug', 'LIKE', '%'.$request->q.'%');
        });

        $list = $list->take(10)->get();

        return response()->json($list);

    }
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------
    //----------------------------------------------------------


}
