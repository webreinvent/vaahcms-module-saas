<?php namespace VaahCms\Modules\Saas\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;
use WebReinvent\VaahCms\Models\User;

class App extends Model {

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $connection= 'mysql';
    //-------------------------------------------------
    protected $table = 'vh_saas_apps';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'app_type',
        'name',
        'slug',
        'excerpt',
        'version',
        'version_number',
        'relative_path',
        'migration_path',
        'seed_class',
        'sample_data_class',
        'count_tenants_active',
        'count_tenants',
        'activated_at',
        'is_active',
        'is_deactivated_at',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
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
    public function tenants()
    {
        return $this->belongsToMany( Tenant::class,
            'vh_saas_tenant_apps',
            'vh_saas_app_id', 'vh_saas_tenant_id'
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


        //composer get version
        if(!isset($inputs['version']))
        {
            $composer_path = base_path($inputs['relative_path']).'/composer.json';

            if(!\File::exists($composer_path))
            {
                $response['status'] = 'failed';
                $response['errors'][] = 'composer.json does not exist at '.$composer_path;

                return $response;
            }

            $composer = json_decode(file_get_contents($composer_path), true);

            if(!isset($composer['version']))
            {
                $response['status'] = 'failed';
                $response['errors'][] = 'version variable does not exist in composer.json at '.$composer_path;

                return $response;
            }

            $inputs['version'] = $composer['version'];
            $inputs['version_number'] = (int) filter_var($inputs['version'] , FILTER_SANITIZE_NUMBER_INT);
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


    public static function recountRelations()
    {
        $list = static::withTrashed()->select('id')->get();

        if($list)
        {
            foreach ($list as $item)
            {
                $item->count_tenants_active = static::countActiveTenants($item->id);
                $item->count_tenants = Tenant::all()->count();
                $item->save();
            }
        }

    }


    //-------------------------------------------------
    public static function countActiveTenants($id)
    {

        $app = static::withTrashed()->where('id', $id)->first();

        if(!$app)
        {
            return 0;
        }

        return $app->tenants()->wherePivot('is_active', 1)->count();
    }
    //-------------------------------------------------
    //-------------------------------------------------
    public static function getList($request)
    {

        if(isset($request->recount) && $request->recount == true)
        {
            static::recountRelations();
            Tenant::recountRelations();
        }

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

            $item = static::where('id', $id)->withTrashed()->forceDelete();

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
            'relative_path' => 'required|max:150',
            'migration_path' => 'max:150',
            'seed_class' => 'max:150',
            'sample_data_class' => 'max:150',
            'excerpt' => 'max:255',
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
    public static function countTenants($id)
    {

        $item = static::withTrashed()->where('id', $id)->first();

        if(!$item)
        {
            return 0;
        }

        return $item->tenants()
            ->count();
    }
    //-------------------------------------------------
    public static function countTenantsActive($id)
    {

        $item = static::withTrashed()->where('id', $id)->first();

        if(!$item)
        {
            return 0;
        }

        return $item->tenants()
            ->wherePivotNotNull('is_active')
            ->count();

    }
    //-------------------------------------------------

    //-------------------------------------------------
    public static function updateCounts(App $app)
    {
        $app->count_tenants_active = static::countTenantsActive($app->id);
        $app->count_tenants = static::countTenants($app->id);
        $app->save();
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
    public static function getItemTenants($request, $id)
    {
        $item = static::withTrashed()->where('id', $id)->first();
        $response['data']['item'] = $item;

        if($request->has("q"))
        {
            $list = $item->tenants()->where(function ($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('slug', 'LIKE', '%'.$request->q.'%');
            });
        } else
        {
            $list = $item->tenants();
        }


        $list->orderBy('pivot_is_active', 'desc');


        $list = $list->paginate(config('vaahcms.per_page'));


        $response['data']['list'] = $list;
        $response['status'] = 'success';

        return $response;


    }
    //-------------------------------------------------
    public static function syncAppsWithTenants()
    {
        $all_tenants = Tenant::select('id')->get()->pluck('id')->toArray();
        $all_apps = App::select('id', 'version', 'version_number')->get();

        if(!$all_apps)
        {
            return false;
        }



        foreach ($all_apps as $app)
        {

            $pivots = [
                'version' => $app->version,
                'version_number' => $app->version_number,
            ];

            $pivotData = array_fill(0, count($all_tenants), $pivots);
            $syncData  = array_combine($all_tenants, $pivotData);

            $app->tenants()->syncWithoutDetaching($syncData);

        }

    }
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}
