<?php

namespace App\Http\Controllers\Admin\Audit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class PropertyLog extends Controller
{
    public function index(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property', compact('activity', 'lastday'));
    }

    public function assessment(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-assessment');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        //dd("hi");
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-assessment', compact('activity', 'lastday'));
    }

    public function payment(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-payment');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-payment', compact('activity', 'lastday'));
    }

    public function landlord(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-landlord');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-landlord', compact('activity', 'lastday'));
    }

    public function occupancyDetail(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-occupancy-detail');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-occupancy-detail', compact('activity', 'lastday'));
    }

    public function occupancy(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-occupancy');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-occupancy', compact('activity', 'lastday'));
    }

    public function geoRegistry(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-geo-registry');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-geo-registry', compact('activity', 'lastday'));
    }

    public function registryMeter(Request $request)
    {
        $activity = Activity::with('causer', 'subject')->where('log_name', 'property-registry-meter');
        if ($request->last_at) {
            $date = \Carbon\Carbon::today()->subDays($request->last_at);
            $activity = $activity->where('created_at', '>=', $date);
        }
        $activity = $activity->latest()->paginate();
        $lastday = lastday();
        return view('admin.audit.property-registry-meter', compact('activity', 'lastday'));
    }
}
