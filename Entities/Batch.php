<?php namespace WebReinvent\VaahCms\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Batch extends Model {

    //-------------------------------------------------
    protected $table = 'job_batches';
    //-------------------------------------------------
    protected $dates = [
        'cancelled_at',
        'created_at',
        'finished_at'
    ];
    //-------------------------------------------------
    protected $casts = [
        'cancelled_at'  => 'date:Y-m-d H:i:s',
        'created_at'  => 'date:Y-m-d H:i:s',
        'finished_at'  => 'date:Y-m-d H:i:s',
    ];
    //-------------------------------------------------
    protected $dateFormat = 'Y-m-d H:i:s';
    //-------------------------------------------------
    protected $fillable = [
    ];

    //-------------------------------------------------
    protected $appends  = [
        'count_failed_jobs'
    ];
    //-------------------------------------------------
    public function getFailedJobIdsAttribute($value)
    {
        if($value)
        {
            $value = json_decode($value, true);
        }
        return $value;
    }
    //-------------------------------------------------
    public function getCountFailedJobsAttribute()
    {
        $value = 0;

        if(
            isset($this->failed_job_ids)
            && !empty($this->failed_job_ids)
            && is_array($this->failed_job_ids)
        ){
            $value = count($this->failed_job_ids);
        }

        return $value;
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
    public static function getList($request)
    {


        $list = self::orderBy('created_at', 'DESC');

        if($request['trashed'] == 'true')
        {

            $list->withTrashed();
        }

        if(isset($request->from) && isset($request->to))
        {
            $list->betweenDates($request['from'],$request['to']);
        }


        if(isset($request->q))
        {

            $list->where(function ($q) use ($request){
                $q->where('name', 'LIKE', '%'.$request->q.'%');
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

        $item = self::where('id', $id)
        ->first();

        $response['status'] = 'success';
        $response['data'] = $item;

        return $response;

    }
    //-------------------------------------------------
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
            $item = self::where('id',$id)->withTrashed()->first();

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
            $item = self::withTrashed()->where('id', $id)->first();
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
            $item = self::withTrashed()->where('id', $id)->first();
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
            $item = self::where('id', $id)->withTrashed()->first();
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

    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}
