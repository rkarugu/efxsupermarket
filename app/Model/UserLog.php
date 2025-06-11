<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $guarded = [];

    protected $table = "user_logs";

    public static function getDataModel($limit, $start, $search, $orderby, $order, $request)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'id';
        $query = UserLog::query();
        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('user_name', 'LIKE', '%' . $search . '%');
                $q->orWhere('user_ip', 'LIKE', '%' . $search . '%');
                $q->orWhere('user_agent', 'LIKE', '%' . $search . '%');

            });
        }

        if ($request) {
            if ($request->date) {
                $query->whereDate('created_at', '>=', Carbon::parse($request->date)->format('Y-m-d H:i:s'));
            }

            if ($request->todate) {
                $query->whereDate('created_at', '<=', Carbon::parse($request->todate)->format('Y-m-d H:i:s'));
            }
        }

        $count = $query->count('id');
        $query = $query->orderBy($orderby, $order)->limit($limit)->offset($start)->get();
        return ['count' => $count, 'response' => $query];
    }
}
