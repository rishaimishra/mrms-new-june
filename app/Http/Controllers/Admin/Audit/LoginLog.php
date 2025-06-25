<?php

namespace App\Http\Controllers\Admin\Audit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class LoginLog extends Controller
{
    public function userLoginAudit(Request $request)
    {
        $activity = Activity::with('causer')->where('log_name', 'login');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.user', compact('activity', 'lastday'));
    }

    public function adminLoginAudit(Request $request)
    {

        $activity = Activity::with('causer')->where('log_name', 'admin-login');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.admin',  compact('activity', 'lastday'));
    }
}
