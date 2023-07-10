<?php namespace VaahCms\Modules\Saas\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;
use WebReinvent\VaahCms\Models\User;

class Server extends Model {

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'vh_saas_servers';
    //-------------------------------------------------
    protected $dates = [
        'is_active_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'host_type',
        'driver',
        'host',
        'port',
        'username',
        'sslmode',
        'count_tenants',
        'count_db_instances',
        'is_active_at',
        'is_active',
        'meta',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    //-------------------------------------------------
    protected $hidden = [
        'password',
    ];

    //-------------------------------------------------
    protected $appends  = [
    ];
    //-------------------------------------------------
    public function setPasswordAttribute($value)
    {
        if($value)
        {
            $this->attributes['password'] = Crypt::encrypt($value);
        }
    }
    //-------------------------------------------------
    public function setMetaAttribute($value)
    {

        if(isset($value['cpanel_api_token']))
        {
            $value['cpanel_api_token'] = \Crypt::encrypt($value['cpanel_api_token']);
        }

        if(isset($value['password']))
        {
            $value['password'] = \Crypt::encrypt($value['password']);
        }

        $this->attributes['meta'] = json_encode($value);
    }
    //-------------------------------------------------
    public function getMetaAttribute($value)
    {
        return json_decode($value);
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
    public function tenants()
    {
        return $this->hasMany(Tenant::class,
            'vh_saas_server_id', 'id'
        );
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
        $item->password = $inputs['password'];
        $item->save();

        static::updateCounts($item);


        $response['status'] = 'success';
        $response['data']['item'] = $item;
        $response['messages'][] = 'Saved successfully.';
        return $response;

    }
    //-------------------------------------------------
    public static function updateCounts(Server $server)
    {
        $server->count_tenants = static::countTenants($server->id);
        $server->count_db_instances = static::countDatabaseInstances($server->id);
        $server->save();
    }
    //-------------------------------------------------
    public static function getList($request)
    {

        if($request['sort_by'])
        {
            $list = static::orderBy($request['sort_by'], $request['sort_order']);
        }else{
            $list = static::orderBy('id', $request['sort_order']);
        }

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
            if(isset($request['search_by']) && $request['search_by'])
            {
                $list->where($request['search_by'], 'LIKE', '%'.$request->q.'%');

            }else{
                $list->where(function ($q) use ($request){
                    $q->where('name', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('slug', 'LIKE', '%'.$request->q.'%');
                });
            }
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
        $update->fill($input);
        $update->slug = Str::slug($input['slug']);
        if(isset($input['password'])){
            $update->password = $input['password'];
        }
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
            'host_type' => 'required|max:150',
            'driver' => 'required|max:150',
            'host' => 'required|max:150',
            'port' => 'required|max:150',
        );

        if(isset($inputs['host_type']) && $inputs['host_type'] == 'MySql')
        {
            $rules['username'] = 'required|max:150';
        }


        $validator = \Validator::make( $inputs, $rules);
        if ( $validator->fails() ) {

            $errors             = errorsToArray($validator->errors());
            $response['status'] = 'failed';
            $response['errors'] = $errors;
            return $response;
        }

    }
    //-------------------------------------------------
    public static function getActiveItems()
    {
        $item = static::whereNotNull('is_active_at')->get();
        return $item;
    }
    //-------------------------------------------------
    public static function countTenants($id)
    {

        $item = static::withTrashed()->where('id', $id)->first();

        if(!$item)
        {
            return 0;
        }

        return $item->tenants()->count();

    }
    //-------------------------------------------------
    public static function countDatabaseInstances($id)
    {

        $item = static::withTrashed()->where('id', $id)->first();

        if(!$item)
        {
            return 0;
        }

        return $item->tenants()
            ->whereNotNull('vh_saas_tenants.is_database_created_at')
            ->count();
    }
    //-------------------------------------------------

    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}
