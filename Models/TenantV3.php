<?php namespace VaahCms\Modules\Saas\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Faker\Factory;
use VaahCms\Modules\Saas\Entities\App;
use VaahCms\Modules\Saas\Entities\Server;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\DatabaseManager;
use WebReinvent\VaahCms\Models\VaahModel;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;
use WebReinvent\VaahCms\Models\User;
use WebReinvent\VaahCms\Libraries\VaahSeeder;


class TenantV3 extends VaahModel
{

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'vh_saas_tenants';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
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
        'database_username',
        'database_password',
        'database_sslmode',
        'database_charset',
        'database_collation',
        'is_database_created_at',
        'is_database_user_created_at',
        'is_database_user_assigned_at',
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
    protected $fill_except = [

    ];

    //-------------------------------------------------
    protected $appends = [
    ];

    //-------------------------------------------------
    protected function serializeDate(DateTimeInterface $date)
    {
        $date_time_format = config('settings.global.datetime_format');
        return $date->format($date_time_format);
    }

    //-------------------------------------------------
    public static function getUnFillableColumns()
    {
        return [
            'uuid',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }

    //-------------------------------------------------

    public function setMetaAttribute($value)
    {
        $meta = [];
        $this->attributes['meta'] = json_encode($meta);
    }
//-------------------------------------------------
    public function getMetaAttribute($value)
    {
        if($value && $value!='null'){
            return json_decode($value);
        }else{
            return json_decode('{}');
        }

    }
    //-------------------------------------------------
    public static function getFillableColumns()
    {
        $model = new self();
        $except = $model->fill_except;
        $fillable_columns = $model->getFillable();
        $fillable_columns = array_diff(
            $fillable_columns, $except
        );
        return $fillable_columns;
    }
    //-------------------------------------------------
    public static function getEmptyItem()
    {
        $model = new self();
        $fillable = $model->getFillable();
        $empty_item = [];
        foreach ($fillable as $column)
        {
            $empty_item[$column] = null;
            if($column =='meta'){
                $empty_item[$column] =[
                    'ssl_key_path' => null,
                   'ssl_cert_path' =>null,
                   'ssl_ca_path' => null,
                ];
            }

        }

        $empty_item['is_active'] = 1;

        return $empty_item;
    }

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
    public function tenantApps()
    {
        return $this->hasMany(TenantAppV3::class,'vh_saas_tenant_id','id');
    }
    //-------------------------------------------------
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    public function server(){
        return $this->belongsTo(ServerV3::class,'vh_saas_server_id','id');
    }

    //-------------------------------------------------
    public function scopeExclude($query, $columns)
    {
        return $query->select(array_diff($this->getTableColumns(), $columns));
    }

    //-------------------------------------------------
    public function scopeBetweenDates($query, $from, $to)
    {

        if ($from) {
            $from = \Carbon::parse($from)
                ->startOfDay()
                ->toDateTimeString();
        }

        if ($to) {
            $to = \Carbon::parse($to)
                ->endOfDay()
                ->toDateTimeString();
        }

        $query->whereBetween('updated_at', [$from, $to]);
    }

   // -------------------------------------------------
    public static function createItem($request)
    {

        $inputs = $request->all();
        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }


        // check if name exist
        $item = self::where('name', $inputs['name'])->withTrashed()->first();

        if ($item) {
            $error_message = "This name is already exist".($item->deleted_at?' in trash.':'.');
            $response['success'] = false;
            $response['messages'][] = $error_message;
            return $response;
        }

        // check if slug exist
        $item = self::where('slug', $inputs['slug'])->withTrashed()->first();

        if ($item) {
            $error_message = "This slug is already exist".($item->deleted_at?' in trash.':'.');
            $response['success'] = false;
            $response['messages'][] = $error_message;
            return $response;
        }

        $item = new self();
        $item->fill($inputs);
        $item->save();

        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");
        return $response;

    }
    //-------------------------------------------------
    public function scopeGetSorted($query, $filter)
    {
        if(!isset($filter['sort']))
        {
            return $query->orderBy('id', 'desc');
        }

        $sort = $filter['sort'];


        $direction = Str::contains($sort, ':');

        if(!$direction)
        {
            return $query->orderBy($sort, 'asc');
        }

        $sort = explode(':', $sort);

        return $query->orderBy($sort[0], $sort[1]);
    }
    //-------------------------------------------------
    public function scopeIsActiveFilter($query, $filter)
    {

        if(!isset($filter['is_active'])
            || is_null($filter['is_active'])
            || $filter['is_active'] === 'null'
        )
        {
            return $query;
        }
        $is_active = $filter['is_active'];

        if($is_active === 'true' || $is_active === true)
        {
            return $query->where('is_active', 1);
        } else{
            return $query->where(function ($q){
                $q->whereNull('is_active')
                    ->orWhere('is_active', 0);
            });
        }

    }
    //-------------------------------------------------
    public function scopeTrashedFilter($query, $filter)
    {

        if(!isset($filter['trashed']))
        {
            return $query;
        }
        $trashed = $filter['trashed'];

        if($trashed === 'include')
        {
            return $query->withTrashed();
        } else if($trashed === 'only'){
            return $query->onlyTrashed();
        }

    }
    //-------------------------------------------------
    public function scopeSearchFilter($query, $filter)
    {

        if(!isset($filter['q']))
        {
            return $query;
        }
        $search_array = explode(' ',$filter['q']);
        foreach ($search_array as $search_item){
            $query->where(function ($q1) use ($search_item) {
                $q1->where('name', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('slug', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('id', 'LIKE', $search_item . '%');
            });
        }

    }
    //-------------------------------------------------
    public static function getList($request)
    {
        if ($request->has('recount') && $request->recount == true) {

            App::recountRelations();
            Tenant::recountRelations();
        }
        $list = self::getSorted($request->filter);
        $list->isActiveFilter($request->filter);
        $list->trashedFilter($request->filter);
        $list->searchFilter($request->filter);

        $rows = config('vaahcms.per_page');

        if($request->has('rows'))
        {
            $rows = $request->rows;
        }

        $list = $list->paginate($rows);

        $response['success'] = true;
        $response['data'] = $list;

        return $response;


    }

    //-------------------------------------------------
    public static function updateList($request)
    {

        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
        );


        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        if(isset($inputs['items']))
        {
            $items_id = collect($inputs['items'])
                ->pluck('id')
                ->toArray();
        }

        $items = self::whereIn('id', $items_id);

        switch ($inputs['type']) {
            case 'deactivate':
                $items->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'activate':
                $items->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'trash':
                self::whereIn('id', $items_id)
                    ->get()->each->delete();
                break;
            case 'restore':
                self::whereIn('id', $items_id)->onlyTrashed()
                    ->get()->each->restore();
                break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }

    //-------------------------------------------------
    public static function deleteList($request): array
    {

        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
            'items' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
            'items.required' => trans("vaahcms-general.select_items"),
        );

        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }
        // Collect the IDs of tenants to delete
        $items_id = collect($inputs['items'])->pluck('id')->toArray();
        $tenants = self::whereIn('id', $items_id)->with('tenantApps')->get();
        foreach ($tenants as $tenant) {
            $tenant->tenantApps()->forceDelete();
        }
        self::whereIn('id', $items_id)->forceDelete();

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
     public static function listAction($request, $type): array
    {

        $list = self::query();

        if($request->has('filter')){
            $list->getSorted($request->filter);
            $list->isActiveFilter($request->filter);
            $list->trashedFilter($request->filter);
            $list->searchFilter($request->filter);
        }

        switch ($type) {
            case 'activate-all':
                $list->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'deactivate-all':
                $list->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'trash-all':
                $list->get()->each->delete();
                break;
            case 'restore-all':
                $list->onlyTrashed()->get()
                    ->each->restore();
                break;
            case 'delete-all':
                $list->forceDelete();
                break;
            case 'create-100-records':
            case 'create-1000-records':
            case 'create-5000-records':
            case 'create-10000-records':

            if(!config('saas.is_dev')){
                $response['success'] = false;
                $response['errors'][] = 'User is not in the development environment.';

                return $response;
            }

            preg_match('/-(.*?)-/', $type, $matches);

            if(count($matches) !== 2){
                break;
            }

            self::seedSampleItems($matches[1]);
            break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
    public static function getItem($id)
    {

        $item = self::where('id', $id)
            ->with(['server','createdByUser', 'updatedByUser', 'deletedByUser'])
            ->withTrashed()
            ->first();

        if(!$item)
        {
            $response['success'] = false;
            $response['errors'][] = 'Record not found with ID: '.$id;
            return $response;
        }

        $response['success'] = true;

        $response['data'] = $item;


        return $response;

    }
    //-------------------------------------------------
    public static function updateItem($request, $id)
    {
        $inputs = $request->all();

        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }

        // check if name exist
        $item = self::where('id', '!=', $id)
            ->withTrashed()
            ->where('name', $inputs['name'])->first();

         if ($item) {
             $error_message = "This name is already exist".($item->deleted_at?' in trash.':'.');
             $response['success'] = false;
             $response['errors'][] = $error_message;
             return $response;
         }

         // check if slug exist
         $item = self::where('id', '!=', $id)
             ->withTrashed()
             ->where('slug', $inputs['slug'])->first();

         if ($item) {
             $error_message = "This slug is already exist".($item->deleted_at?' in trash.':'.');
             $response['success'] = false;
             $response['errors'][] = $error_message;
             return $response;
         }

        $item = self::where('id', $id)->withTrashed()->first();
        $item->fill($inputs);


        $item->save();

        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");
        return $response;

    }
    //-------------------------------------------------
    public static function deleteItem($request, $id): array
    {
        $item = self::where('id', $id)->withTrashed()->first();
        if (!$item) {
            $response['success'] = false;
            $response['errors'][] = trans("vaahcms-general.record_does_not_exist");
            return $response;
        }

        $item->forceDelete();

        $response['success'] = true;
        $response['data'] = [];
        $response['messages'][] = trans("vaahcms-general.record_has_been_deleted");

        return $response;
    }
    //-------------------------------------------------
    public static function itemAction($request, $id, $type): array
    {
        switch($type)
        {
            case 'activate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => 1]);
                break;
            case 'deactivate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => null]);
                break;
            case 'trash':
                self::find($id)
                    ->delete();
                break;
            case 'restore':
                self::where('id', $id)
                    ->onlyTrashed()
                    ->first()->restore();
                break;
        }

        return self::getItem($id);
    }
    //-------------------------------------------------

    public static function validation($inputs)
    {

        $rules = array(
            'vh_saas_server_id' => 'required|max:150',
            'name' => 'required|max:150',
            'slug' => 'required|max:150',
            'path' => 'max:150',
            'domain' => 'max:150',
            'sub_domain' => 'max:150',
            'database_name' => 'required|alpha_dash|max:20',
            'database_username' => 'required|alpha_dash|max:20',
            'database_charset' => 'max:150',
            'database_collation' => 'max:150',
            'notes' => 'max:255',
        );

        $validator = \Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $messages = $validator->errors();
            $response['success'] = false;
            $response['errors'] = $messages->all();
            return $response;
        }

        $response['success'] = true;
        return $response;

    }

    //-------------------------------------------------
    public static function getActiveItems()
    {
        $item = self::where('is_active', 1)
            ->withTrashed()
            ->first();
        return $item;
    }

    //-------------------------------------------------
    //-------------------------------------------------
    public static function seedSampleItems($records=100)
    {

        $i = 0;

        while($i < $records)
        {
            $inputs = self::fillItem(false);

            $item =  new self();
            $item->fill($inputs);
            $item->save();

            $i++;

        }

    }


    //-------------------------------------------------
    public static function fillItem($is_response_return = true)
    {
        $request = new Request([
            'model_namespace' => self::class,
            'except' => self::getUnFillableColumns()
        ]);
        $fillable = VaahSeeder::fill($request);
        if(!$fillable['success']){
            return $fillable;
        }
        $inputs = $fillable['data']['fill'];

        $faker = Factory::create();

        /*
         * You can override the filled variables below this line.
         * You should also return relationship from here
         */

        if(!$is_response_return){
            return $inputs;
        }

        $response['success'] = true;
        $response['data']['fill'] = $inputs;
        return $response;
    }

    //-------------------------------------------------
    public static function createDatabase($inputs, $tenant_column_name = 'id')
    {
        // Validation rules
        $rules = [
            'vh_saas_server_id' => 'required|integer',
            'tenant_column_value' => 'required',
        ];

        // Validate inputs
        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        try {
            // Fetch the tenant
            $item = self::where($tenant_column_name, $inputs['tenant_column_value'])
                ->withTrashed()
                ->first();

            if (!$item) {
                return [
                    'success' => false,
                    'errors' => ['Tenant not found.'],
                ];
            }

            // Fetch the server
            $server = ServerV3::find($inputs['vh_saas_server_id']);

            if (!$server) {
                return [
                    'success' => false,
                    'errors' => ['Server not found.'],
                ];
            }

            // Instantiate DatabaseManager
            $db_manager = new DatabaseManager($server, $item);

            // Attempt to create the database
            $response = $db_manager->createDatabase();

            if ($response['status'] === 'success') {
                $item->is_active = 1;
                $item->activated_at = now();
                $item->is_database_created_at = now();
                $item->is_deactivated_at = null;
                $item->save();

                $response['data'] = [];
            }

            return $response;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    public static function deleteDatabase($inputs, $tenant_column_name = 'id')
    {
        // Validation rules
        $rules = [
            'vh_saas_server_id' => 'required|integer',
            'tenant_column_value' => 'required',
        ];

        // Validate inputs
        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        try {
            // Fetch the tenant
            $item = self::where($tenant_column_name, $inputs['tenant_column_value'])
                ->withTrashed()
                ->first();

            if (!$item) {
                return [
                    'success' => false,
                    'errors' => ['Tenant not found.'],
                ];
            }

            // Fetch the server
            $server = ServerV3::find($inputs['vh_saas_server_id']);

            if (!$server) {
                return [
                    'success' => false,
                    'errors' => ['Server not found.'],
                ];
            }

            // Instantiate DatabaseManager
            $db_manager = new DatabaseManager($server, $item);

            // Attempt to delete the database
            $response = $db_manager->deleteDatabase();

            if ($response['status'] === 'success') {
                $item->is_active = null;
                $item->activated_at = null;
                $item->is_database_created_at = null;
                $item->is_database_user_assigned_at = null;
                $item->is_deactivated_at = now();
                $item->save();

                $response['data'] = [];
            }

            return $response;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    public static function createDatabaseUser($inputs, $tenant_column_name = 'id')
    {
        // Validation rules
        $rules = [
            'vh_saas_server_id' => 'required|integer',
            'tenant_column_value' => 'required',
        ];

        // Validate inputs
        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        try {
            // Fetch the tenant
            $item = self::where($tenant_column_name, $inputs['tenant_column_value'])
                ->withTrashed()
                ->first();

            if (!$item) {
                return [
                    'success' => false,
                    'errors' => ['Tenant not found.'],
                ];
            }

            // Fetch the server
            $server = ServerV3::find($inputs['vh_saas_server_id']);

            if (!$server) {
                return [
                    'success' => false,
                    'errors' => ['Server not found.'],
                ];
            }

            // Instantiate DatabaseManager
            $db_manager = new DatabaseManager($server, $item);

            // Attempt to create the database user
            $response = $db_manager->createDatabaseUser();

            if ($response['status'] === 'success') {
                $item->is_database_user_created_at = now();
                $item->save();
                $response['data'] = [];
            }

            return $response;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    public static function assignUserToDatabase($inputs, $tenant_column_name = 'id')
    {
        // Validation rules
        $rules = [
            'vh_saas_server_id' => 'required|integer',
            'tenant_column_value' => 'required',
        ];

        // Validate inputs
        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        try {
            // Fetch the tenant
            $item = self::where($tenant_column_name, $inputs['tenant_column_value'])
                ->withTrashed()
                ->first();

            if (!$item) {
                return [
                    'success' => false,
                    'errors' => ['Tenant not found.'],
                ];
            }

            // Fetch the server
            $server = ServerV3::find($inputs['vh_saas_server_id']);

            if (!$server) {
                return [
                    'success' => false,
                    'errors' => ['Server not found.'],
                ];
            }

            // Instantiate DatabaseManager
            $db_manager = new DatabaseManager($server, $item);

            // Attempt to assign the user to the database
            $response = $db_manager->assignUserToDatabase();

            if ($response['status'] === 'success') {
                $item->is_database_user_assigned_at = now();
                $item->save();
                $response['data'] = [];
            }

            return $response;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    public static function deleteDatabaseUser($tenant_column_value, $tenant_column_name='id')
    {

        $item = self::where($tenant_column_name, $tenant_column_value)->withTrashed()->first();
        $server = ServerV3::find($item->vh_saas_server_id);

        $db_manager = new DatabaseManager($server, $item);
        $response = $db_manager->deleteDatabaseUser();

        if($response['status'] == 'success')
        {
            $item->is_database_user_created_at = null;
            $item->is_database_user_assigned_at = null;
            $item->save();
            $response['data'] = [];
        }

        return $response;
    }

    public static function databaseActionValidation($value, $key = 'id')
    {
        $response = ['status' => 'failed', 'errors' => []];

        if (!$value) {
            $response['status'] = 'failed';
            $response['errors'][] = 'Invalid value provided.';
            return $response;
        }

        try {
            // Validate the tenant
            $tenant = self::where($key, $value)->first();
            if (!$tenant) {
                $response['status'] = 'failed';
                $response['errors'][] = 'Tenant does not exist.';
                return $response;
            }

            if (!$tenant->is_active) {
                $response['status'] = 'failed';
                $response['errors'][] = 'Tenant is not active.';
                return $response;
            }

            // Validate the server
            $server = ServerV3::find($tenant->vh_saas_server_id);
            if (!$server) {
                $response['status'] = 'failed';
                $response['errors'][] = "Tenant's server does not exist.";
                return $response;
            }

            if (!$server->is_active) {
                $response['status'] = 'failed';
                $response['errors'][] = "Tenant's server is not active.";
                return $response;
            }

            // Check server connection
            $db_manager = new DatabaseManager($server, $tenant);
            $is_connected = $db_manager->testServerConnection();
            if ($is_connected['status'] === 'failed') {
                return $is_connected;
            }

            // Check if the database exists
            $db_exist = $db_manager->databaseExists();
            if ($db_exist['status'] === 'failed') {
                return $db_exist;
            }

            // Validation successful
            $response['status'] = 'success';
            $response['message'] = 'All validations passed.';
            $response['data'] = [];

            return $response;

        } catch (\Exception $e) {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
            return $response;
        }
    }

    public static function migrate($inputs, $tenant_column_value, $tenant_column_name = 'id')
    {
        $response = ['status' => 'failed', 'errors' => []];

        if (!$inputs) {
            $response['status'] = 'failed';
            $response['errors'][] = 'No inputs provided.';
            return $response;
        }

        try {
            // Validation rules
            $rules = [
                'command' => 'required',
            ];

            if (isset($inputs['command'])) {
                $rules['path'] = 'required';
            }

            // Validate inputs
            $validator = Validator::make($inputs, $rules);
            if ($validator->fails()) {
                $response['status'] = 'failed';
                $response['errors'] = errorsToArray($validator->errors());
                return $response;
            }

            // Validate tenant and server
            $validation = self::databaseActionValidation($tenant_column_value, $tenant_column_name);
            if ($validation['status'] === 'failed') {
                return $validation;
            }

            // Fetch tenant
            $tenant = self::withTrashed()->where($tenant_column_name, $tenant_column_value)->first();

            if (!$tenant) {
                $response['status'] = 'failed';
                $response['errors'][] = 'Tenant not found.';
                return $response;
            }

            if (!$tenant->is_database_created_at) {
                $response['status'] = 'failed';
                $response['errors'][] = 'Tenant database is not created.';
                return $response;
            }

            // Fetch server
            $server = Server::find($tenant->vh_saas_server_id);
            if (!$server) {
                $response['status'] = 'failed';
                $response['errors'][] = "Tenant's server does not exist.";
                return $response;
            }

            // Initialize database manager and configure connection
            $db_manager = new DatabaseManager($server, $tenant);
            $connection = $db_manager->configDbConnection();

            if ($connection['status'] === 'failed') {
                return $connection;
            }

            // Perform migration
            $db_connection_name = $tenant->db_connection_name;
            $response = \VaahArtisan::migrate($inputs['command'], $inputs['path'], $db_connection_name);

            if (isset($response['status']) && $response['status'] === 'failed') {
                return $response;
            }

            // Successful migration
            $response['status'] = 'success';
            $response['messages'][] = 'Migration completed successfully.';
            $response['data'] = $response['data'] ?? [];

            return $response;

        } catch (\Exception $e) {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
            return $response;
        }
    }

    public static function seed($inputs, $tenant_column_value, $tenant_column_name = 'id')
    {
        $response = ['status' => 'failed', 'errors' => []];

        if (!$inputs) {
            $response['status'] = 'failed';
            $response['errors'][] = 'No inputs provided.';
            return $response;
        }

        try {
            // Validation rules
            $rules = [
                'command' => 'required',
            ];

            if (isset($inputs['command']) && $inputs['command'] === 'db:seed') {
                $rules['class'] = 'required';
            }

            // Validate inputs
            $validator = Validator::make($inputs, $rules);
            if ($validator->fails()) {
                $response['status'] = 'failed';
                $response['errors'] = errorsToArray($validator->errors());
                return $response;
            }

            // Validate tenant and server
            $validation = self::databaseActionValidation($tenant_column_value, $tenant_column_name);
            if ($validation['status'] === 'failed') {
                return $validation;
            }

            // Fetch tenant
            $tenant = self::withTrashed()->where($tenant_column_name, $tenant_column_value)->first();

            if (!$tenant) {
                $response['status'] = 'failed';
                $response['errors'][] = 'Tenant not found.';
                return $response;
            }

            if (!$tenant->is_database_created_at) {
                $response['status'] = 'failed';
                $response['errors'][] = 'Tenant database is not created.';
                return $response;
            }

            // Fetch server
            $server = Server::find($tenant->vh_saas_server_id);
            if (!$server) {
                $response['status'] = 'failed';
                $response['errors'][] = "Tenant's server does not exist.";
                return $response;
            }

            // Initialize database manager and configure connection
            $db_manager = new DatabaseManager($server, $tenant);
            $connection = $db_manager->configDbConnection();

            if ($connection['status'] === 'failed') {
                return $connection;
            }

            // Execute seeding
            $db_connection_name = $tenant->db_connection_name;
            $class = $inputs['class'] ?? null;

            $response = \VaahArtisan::seed($inputs['command'], $class, $db_connection_name);

            if (isset($response['status']) && $response['status'] === 'failed') {
                return $response;
            }

            // Successful seeding
            $response['status'] = 'success';
            $response['messages'][] = 'Seeding completed successfully.';
            $response['data'] = $response['data'] ?? [];

            return $response;

        } catch (\Exception $e) {
            $response['status'] = 'failed';
            $response['errors'][] = $e->getMessage();
            return $response;
        }
    }

    public static function countApps($id)
    {
        // Fetch tenant with trashed
        $tenant = self::withTrashed()->find($id);

        // Return count of related apps or 0 if tenant not found
        return $tenant ? $tenant->apps()->count() : 0;
    }
//-------------------------------------------------
    public static function countAppsActive($id)
    {
        // Fetch tenant with trashed
        $tenant = self::withTrashed()->find($id);

        // Return count of active apps or 0 if tenant not found
        return $tenant
            ? $tenant->apps()->wherePivotNotNull('is_active')->count()
            : 0;
    }
//-------------------------------------------------
    public static function updateCounts(Tenant $tenant)
    {
        // Update active and total app counts for a specific tenant
        $tenant->count_apps_active = self::countAppsActive($tenant->id);
        $tenant->count_apps = self::countApps($tenant->id);

        // Save updated counts
        $tenant->save();
    }
//-------------------------------------------------
    public static function updateCountsForAll()
    {
        // Fetch all tenants and update counts in bulk
        self::withTrashed()->chunk(100, function ($tenants) {
            foreach ($tenants as $tenant) {
                self::updateCounts($tenant);
            }
        });
    }
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}
