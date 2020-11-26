<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\DatabaseManager;


class ServersController extends Controller
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


        $data['host_types'] = saas_host_types();

        $data['drivers'] = [
            'mysql',
        ];

        $data['bulk_actions'] = vh_general_bulk_actions();

        $data['database_sslmodes'] = saas_db_ssl_modes();

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);
    }
    //----------------------------------------------------------

    //----------------------------------------------------------
    public function postCreate(Request $request)
    {
        $response = Server::createItem($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getList(Request $request)
    {
        $response = Server::getList($request);
        return response()->json($response);
    }
    //----------------------------------------------------------
    public function getItem(Request $request, $id)
    {
        $response = Server::getItem($id);
        return response()->json($response);
    }

    //----------------------------------------------------------
    public function postStore(Request $request,$id)
    {
        $response = Server::postStore($request,$id);
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

                $response = Server::bulkStatusChange($request);

                break;
            //------------------------------------
            case 'bulk-trash':

                $response = Server::bulkTrash($request);

                break;
            //------------------------------------
            case 'bulk-restore':

                $response = Server::bulkRestore($request);

                break;

            //------------------------------------
            case 'bulk-delete':

                $response = Server::bulkDelete($request);

                break;
            //------------------------------------
            //------------------------------------

        }

        return response()->json($response);

    }
    //----------------------------------------------------------
    public function connect(Request $request)
    {
        $rules = array(
            'host_type' => 'required',
            'driver' => 'required',
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
        );

        $validator = \Validator::make( $request->new_item, $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return response()->json($response);
        }

        $data = [];

        $item = new Server();
        $item->fill($request->new_item);


        $db_manager = new DatabaseManager($item);
        $response = $db_manager->testServerConnection();


        return response()->json($response);

    }
    //----------------------------------------------------------


}
