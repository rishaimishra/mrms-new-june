<?php


namespace App\Http\Controllers\Admin;


use App\Exports\InterestedRealEstateExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RealEstateInterestedUser;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RealEstateReportController extends Controller
{
    protected $users;
    protected $realestateInterestedUser;
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->realestateInterestedUser = RealEstateInterestedUser::join('users as u', 'u.id', 'real_estate_interested_user.user_id')
            ->join('real_estates as re', 're.id', 'real_estate_interested_user.real_estate_id')->select('u.id as user_id', 'u.name as user_name', 're.id as real_estates_id', 're.name as real_estates_name', 'real_estate_interested_user.created_at');


        $this->applyFilters($request);

        $realestateInterestedUser = $this->realestateInterestedUser->latest()->paginate();

        if ($request->download) {
            return Excel::download(new InterestedRealEstateExport($request), 'interested_real_estate.xlsx');
        }

        return view('admin.realestatereport.list', compact('realestateInterestedUser'));
    }

    public function applyFilters($request)
    {

        !$request->user || $this->realestateInterestedUser->where('u.name', 'like', "%{$request->user}%");
        !$request->realEstate || $this->realestateInterestedUser->where('re.name', 'like', "%{$request->realEstate}%");
    }
}
