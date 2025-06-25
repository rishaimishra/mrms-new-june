<?php


namespace App\Http\Controllers\Admin;


use App\Exports\InterestedAutosExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AutoInterestedUser;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AutoReportController extends Controller
{
    protected $users;
    protected $autoInterestedUser;
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $this->autoInterestedUser = AutoInterestedUser::join('users as u', 'u.id', 'auto_interested_user.user_id')
            ->join('autos as au', 'au.id', 'auto_interested_user.auto_id')->select('u.id as user_id', 'u.name as user_name', 'au.id as auto_id', 'au.name as auto_name', 'auto_interested_user.created_at');


        $this->applyFilters($request);

        $autoInterestedUser = $this->autoInterestedUser->latest()->paginate();

        if ($request->download) {
            return Excel::download(new InterestedAutosExport($request), 'interested_autos.xlsx');
        }

        return view('admin.autoreport.list', compact('autoInterestedUser'));
    }

    public function applyFilters($request)
    {
        !$request->user || $this->autoInterestedUser->where('u.name', 'like', "%{$request->user}%");
        !$request->auto || $this->autoInterestedUser->where('au.name', 'like', "%{$request->auto}%");
    }
}
