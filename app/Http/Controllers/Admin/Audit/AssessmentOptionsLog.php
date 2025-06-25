<?php

namespace App\Http\Controllers\Admin\Audit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AssessmentOptionsLog extends Controller
{
    public function propertyCategories(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-categories');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-categories', compact('activity', 'lastday'));
    }


    public function propertyTypes(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-types');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-type', compact('activity', 'lastday'));
    }

    public function wallMaterial(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-wall-materials');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-wall-materials', compact('activity', 'lastday'));
    }

    public function roofMaterial(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-roofs-materials');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-roofs-materials', compact('activity', 'lastday'));
    }

    public function propertyDimensions(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-dimension');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-dimension', compact('activity', 'lastday'));
    }
    public function valueAdded(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-value-added');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-value-added', compact('activity', 'lastday'));
    }
    public function propertyUse(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-use');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-use', compact('activity', 'lastday'));
    }
    public function zones(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-zones');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-zones', compact('activity', 'lastday'));
    }
    public function swimmingPool(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'swimmings');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-swimmings', compact('activity', 'lastday'));
    }
    public function propertyInaccessible(Request $request)
    {

        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-inaccessibles');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-inaccessibles', compact('activity', 'lastday'));
    }
}
