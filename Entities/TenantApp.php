<?php namespace VaahCms\Modules\Saas\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use VaahCms\Modules\Saas\Models\TenantAppV3;
use WebReinvent\VaahCms\Models\User;
use WebReinvent\VaahCms\Models\VaahModel;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;


class TenantApp extends TenantAppV3 {

    use SoftDeletes;
    use CrudWithUuidObservantTrait;


    //-------------------------------------------------
    protected $connection= 'mysql';
    //-------------------------------------------------
    protected $table = 'vh_saas_tenant_apps';
    //-------------------------------------------------
    protected $dates = [
        'last_migrated_at',
        'last_seeded_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'vh_saas_tenant_id',
        'vh_saas_app_id',
        'version',
        'version_number',
        'is_active',
        'last_migrated_at',
        'last_seeded_at',
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
    protected function lastMigratedAt(): Attribute
    {
        return Attribute::make(
            get: function (string $value = null) {
                return self::getUserTimezoneDate($value);
            },
        );
    }
    //-------------------------------------------------
    protected function lastSeededAt(): Attribute
    {
        return Attribute::make(
            get: function (string $value = null) {
                return self::getUserTimezoneDate($value);
            },
        );
    }
    //-------------------------------------------------
    public function tenant()
    {
        return $this->belongsTo(Tenant::class,
            'vh_saas_tenant_id', 'id'
        );
    }
    //-------------------------------------------------
    public function app()
    {
        return $this->belongsTo(App::class,
            'vh_saas_app_id', 'id'
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
        $item->save();

        $response['status'] = 'success';
        $response['data']['item'] = $item;
        $response['messages'][] = 'Saved successfully.';
        return $response;

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

        $list->with(['tenant', 'app'])->has('tenant')->has('app');

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
                if($request['search_by'] == 'tenent'){
                    $list->whereHas('tenant', function ($tenant) use ($request) {
                        $tenant->where('name', 'like',  '%'.$request->q.'%');
                    });
                }elseif($request['search_by'] == 'app'){
                    $list->whereHas('app', function ($app) use ($request) {
                        $app->where('name', 'like',  '%'.$request->q.'%');
                    });
                }

            }else{
                $list->whereHas('tenant', function ($tenant) use ($request) {
                    $tenant->where('name', 'like',  '%'.$request->q.'%');
                });

                $list->orWhereHas('app', function ($app) use ($request) {
                    $app->where('name', 'like',  '%'.$request->q.'%');
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
        ->with(['tenant.server', 'app', 'createdByUser', 'updatedByUser', 'deletedByUser'])
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

        $update = static::where('id',$id)->withTrashed()->first();
        $update->fill($input);
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
            'vh_saas_tenant_id' => 'required|max:150',
            'vh_saas_app_id' => 'required|max:150',
            'notes' => 'max:255',
        );

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
        $item = static::where('is_active', 1)->get();
        return $item;
    }
    //-------------------------------------------------
    public static function syncTenantApps()
    {
        App::syncAppsWithTenants();
        $response['status'] = 'success';
        $response['data'] = [];
        $response['messages'][] = 'Action was successful';

        return $response;
    }
    //-------------------------------------------------
    //-------------------------------------------------


}
