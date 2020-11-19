<?php namespace VaahCms\Modules\Saas\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\DatabaseManager;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;
use WebReinvent\VaahCms\Entities\User;

class Tenant extends Model {

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'vh_saas_tenants';
    //-------------------------------------------------
    protected $dates = [
        'is_database_created_at',
        'activated_at',
        'is_deactivated_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'vh_saas_server_id',
        'name',
        'slug',
        'path',
        'domain',
        'sub_domain',

        'database_name',
        'database_charset',
        'database_collation',
        'is_database_created_at',
        'activated_at',
        'is_active',
        'is_deactivated_at',
        'notes',

        'meta',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    //-------------------------------------------------
    protected $appends  = [

    ];
    //-------------------------------------------------

    //-------------------------------------------------

    public function createdByUser()
    {
        return $this->belongsTo(User::class,
            'created_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function updatedByUser()
    {
        return $this->belongsTo(User::class,
            'updated_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function deletedByUser()
    {
        return $this->belongsTo(User::class,
            'deleted_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }
    //-------------------------------------------------
    public function apps()
    {
        return $this->belongsToMany( App::class,
            'vh_saas_tenant_apps',
            'vh_saas_tenant_id', 'vh_saas_app_id'
        )->withPivot('version',
            'version_number', 'is_active',
            'last_migrated_at', 'last_seeded_at',
            'created_at', 'updated_at');
    }
    //-------------------------------------------------
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }
    //-------------------------------------------------
    public function scopeExclude($query, $columns)
    {
        return $query->select( array_diff( $this->getTableColumns(),$columns) );
    }

    //-------------------------------------------------
    public function scopeBetweenDates($query, $from, $to)
    {

        if($from)
        {
            $from = \Illuminate\Support\Carbon::parse($from)
                ->startOfDay()
                ->toDateTimeString();
        }

        if($to)
        {
            $to = Carbon::parse($to)
                ->endOfDay()
                ->toDateTimeString();
        }

        $query->whereBetween('updated_at',[$from,$to]);
    }
    //-------------------------------------------------

    //-------------------------------------------------
    public static function createItem($request)
    {

        $inputs = $request->new_item;

        $validation = static::validation($inputs);
        if(isset($validation['status']) && $validation['status'] == 'failed')
        {
            return $validation;
        }


        // check if name exist
        $item = static::where('name',$inputs['name'])->first();

        if($item)
        {
            $response['status'] = 'failed';
            $response['errors'][] = "This name is already exist.";
            return $response;
        }


        // check if slug exist
        $item = static::where('slug',$inputs['slug'])->first();

        if($item)
        {
            $response['status'] = 'failed';
            $response['errors'][] = "This slug is already exist.";
            return $response;
        }

        $item = new static();
        $item->fill($inputs);
        $item->slug = Str::slug($inputs['slug']);
        $item->save();

        App::syncAppsWithTenants();

        static::updateCounts($item);

        $response['status'] = 'success';
        $response['data']['item'] = $item;
        $response['messages'][] = 'Saved successfully.';
        return $response;

    }
    //-------------------------------------------------
    public static function getList($request)
    {


        $list = static::orderBy('id', 'desc');

        if($request['trashed'] == 'true')
        {

            $list->withTrashed();
        }

        if(isset($request->from) && isset($request->to))
        {
            $list->betweenDates($request['from'],$request['to']);
        }

        if($request['filter'] && $request['filter'] == '1')
        {

            $list->where('is_active',$request['filter']);
        }elseif($request['filter'] == '10'){

            $list->whereNull('is_active')->orWhere('is_active',0);
        }

        if(isset($request->q))
        {

            $list->where(function ($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('slug', 'LIKE', '%'.$request->q.'%');
            });
        }


        $data['list'] = $list->paginate(config('vaahcms.per_page'));

        $response['status'] = 'success';
        $response['data'] = $data;

        return $response;


    }
    //-------------------------------------------------
    public static function getItem($id)
    {

        $item = static::where('id', $id)
        ->with(['createdByUser', 'updatedByUser', 'deletedByUser'])
        ->withTrashed()
        ->first();

        $response['status'] = 'success';
        $response['data'] = $item;

        return $response;

    }
    //-------------------------------------------------
    public static function postStore($request,$id)
    {

        $input = $request->item;


        $validation = static::validation($input);
        if(isset($validation['status']) && $validation['status'] == 'failed')
        {
            return $validation;
        }

        // check if name exist
        $user = static::where('id','!=',$input['id'])->where('name',$input['name'])->first();

        if($user)
        {
            $response['status'] = 'failed';
            $response['errors'][] = "This name is already exist.";
            return $response;
        }


        // check if slug exist
        $user = static::where('id','!=',$input['id'])->where('slug',$input['slug'])->first();

        if($user)
        {
            $response['status'] = 'failed';
            $response['errors'][] = "This slug is already exist.";
            return $response;
        }

        $update = static::where('id',$id)->withTrashed()->first();

        $update->name = $input['name'];
        $update->slug = Str::slug($input['slug']);
        $update->save();


        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Data updated.';

        return $response;

    }
    //-------------------------------------------------
    public static function bulkStatusChange($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        if(!$request->has('data'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select Status';
            return $response;
        }

        foreach($request->inputs as $id)
        {
            $item = static::where('id',$id)->withTrashed()->first();

            if($item->deleted_at){
                continue ;
            }

            if($request['data']){
                $item->is_active = $request['data']['status'];
            }else{
                if($item->is_active == 1){
                    $item->is_active = 0;
                }else{
                    $item->is_active = 1;
                }
            }
            $item->save();
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;

    }
    //-------------------------------------------------
    public static function bulkTrash($request)
    {


        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }


        foreach($request->inputs as $id)
        {
            $item = static::withTrashed()->where('id', $id)->first();
            if($item)
            {
                $item->delete();
            }
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;


    }
    //-------------------------------------------------
    public static function bulkRestore($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        if(!$request->has('data'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select Status';
            return $response;
        }

        foreach($request->inputs as $id)
        {
            $item = static::withTrashed()->where('id', $id)->first();
            if(isset($item) && isset($item->deleted_at))
            {
                $item->restore();
            }
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;

    }
    //-------------------------------------------------
    public static function bulkDelete($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        if(!$request->has('data'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select Status';
            return $response;
        }

        foreach($request->inputs as $id)
        {
            $item = static::where('id', $id)->withTrashed()->first();
            if($item)
            {
                $item->forceDelete();
            }
        }

        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;


    }
    //-------------------------------------------------

    public static function validation($inputs)
    {

        $rules = array(
            'name' => 'required|max:150',
            'slug' => 'required|max:150',
            'database_name' => 'required|alpha_dash|max:20',
        );

        $validator = \Validator::make( $inputs, $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return $response;
        }

        $exist = static::withTrashed()->where('database_name', $inputs['database_name'])
            ->first();


        if($exist)
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Database name already exist';
            return $response;
        }


    }
    //-------------------------------------------------
    public static function createDatabase($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        $item = static::where('id', $request->inputs)->withTrashed()->first();
        $server = Server::find($item->vh_saas_server_id);

        $db_manager = new DatabaseManager($server, $item);
        $response = $db_manager->createDatabase();

        if($response['status'] == 'success')
        {
            $item->is_active = 1;
            $item->activated_at = \Carbon::now();
            $item->is_database_created_at = \Carbon::now();
            $item->is_deactivated_at = null;
            $item->save();
            $response['data'] = [];
        }

        return $response;
    }
    //-------------------------------------------------
    public static function deleteDatabase($request)
    {

        if(!$request->has('inputs'))
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Select IDs';
            return $response;
        }

        $item = static::where('id', $request->inputs)->withTrashed()->first();
        $server = Server::find($item->vh_saas_server_id);

        $db_manager = new DatabaseManager($server, $item);
        $response = $db_manager->deleteDatabase();

        if($response['status'] == 'success')
        {

            $item->is_active = null;
            $item->activated_at = null;
            $item->is_database_created_at = null;
            $item->is_deactivated_at = \Carbon::now();
            $item->save();

            $response['data'] = [];

        }

        return $response;


    }
    //-------------------------------------------------
    public static function artisanCommandValidation($value, $key='uuid')
    {
        $tenant = static::withTrashed()->where($key, $value)->first();

        if(!$tenant)
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Tenant does not exist';
            return $response;
        }

        //check database is created for the tenant
        if(!$tenant->is_database_created_at)
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Tenant database is not created';
            return $response;
        }

        $server = Server::find($tenant->vh_saas_server_id);

        if(!$server)
        {
            $response['status'] = 'failed';
            $response['errors'][] = 'Tenant database server does not exist';
            return $response;
        }

        $db_manager = new DatabaseManager($server, $tenant);

        //check server connection
        $is_connected = $db_manager->testConnection();
        if($is_connected['status'] == 'failed')
        {
            return $is_connected;
        }

        //check if database does not exist on the server
        $db_exist = $db_manager->databaseExists();
        if($db_exist['status'] == 'failed')
        {
            return $db_exist;
        }
    }
    //-------------------------------------------------
    public static function migrate($inputs, $value, $key='uuid')
    {
        $rules = array(
            'command' => 'required',
        );

        if(isset($inputs['command']))
        {
            $rules['path'] = 'required';
        }

        $validator = Validator::make( $inputs, $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return $response;
        }

        $is_valid = static::artisanCommandValidation($value, $key='uuid');

        if($is_valid['status'] == 'failed')
        {
            return $is_valid;
        }

        $tenant = static::withTrashed()->where($key, $value)->first();
        $server = Server::find($tenant->vh_saas_server_id);
        $db_manager = new DatabaseManager($server, $tenant);

        //connect to database
        $connection = $db_manager->connectToDatabase();

        if(isset($connection['status']) && $connection['status'] == 'failed')
        {
            return $connection;
        }

        $db_connection_name = $connection['data']['connection_name'];

        $response = \VaahArtisan::migrate($inputs['command'], $db_connection_name, $inputs['path']);

        return $response;

    }
    //-------------------------------------------------
    public static function seed($inputs, $value, $key='uuid')
    {
        $rules = array(
            'command' => 'required',
        );

        if(isset($inputs['command']))
        {
            $rules['class'] = 'required';
        }

        $validator = Validator::make( $inputs, $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return $response;
        }

        $is_valid = static::artisanCommandValidation($value, $key='uuid');

        if($is_valid['status'] == 'failed')
        {
            return $is_valid;
        }

        $tenant = static::withTrashed()->where($key, $value)->first();
        $server = Server::find($tenant->vh_saas_server_id);
        $db_manager = new DatabaseManager($server, $tenant);

        //connect to database
        $connection = $db_manager->connectToDatabase();

        if(isset($connection['status']) && $connection['status'] == 'failed')
        {
            return $connection;
        }

        $db_connection_name = $connection['data']['connection_name'];

        $response = \VaahArtisan::seed($inputs['command'], $db_connection_name, $inputs['class']);

        return $response;

    }

    //-------------------------------------------------
    public static function countApps($id)
    {

        $item = static::withTrashed()->where('id', $id)->first();

        if(!$item)
        {
            return 0;
        }

        return $item->apps()
            ->count();
    }
    //-------------------------------------------------
    public static function countAppsActive($id)
    {

        $item = static::withTrashed()->where('id', $id)->first();

        if(!$item)
        {
            return 0;
        }

        return $item->apps()
            ->wherePivotNotNull('is_active')
            ->count();

    }
    //-------------------------------------------------
    public static function updateCounts(Tenant $tenant)
    {
        $tenant->count_apps_active = static::countAppsActive($tenant->id);
        $tenant->count_apps = static::countApps($tenant->id);
        $tenant->save();
    }
    //-------------------------------------------------
    public static function updateCountsForAll()
    {
        $all = static::all();

        if($all)
        {
            foreach($all as $item)
            {
                static::updateCounts($item);
            }
        }
    }
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}
