<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Grids\UsersGrid;
use App\Models\ResidentialCategory;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\Admin\User\UserVerifiedNotification;
use DataTables;
use Grids;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Woo\GridView\DataProviders\EloquentDataProvider;


class EdsaUserController extends AdminController
{


    public function index(Request $request)
    {
        $title = "Last One Year Digital Address Report";
        $subtitle = "Monthly";

        $this->users = User::select('*')->where('is_edsa_agent',1)->orderBy('created_at', 'desc');

        $this->applyFilters($request);

        if ($request->download) {
            return Excel::download(new UsersExport($request), 'digital_addresses.xlsx');
        }
        $users = $this->users->paginate();
        $dataProvider =  new EloquentDataProvider($this->users);
        $datas = User::select(\DB::raw('COUNT(id) as count'));

        if ($request->dwm == 'Daily') {

            $subtitle = "Daily";
            $datas = $datas->addSelect(\DB::raw('DATE_FORMAT(created_at, "%D %b") as month'));
        } else if ($request->dwm == 'Weekly') {
            $subtitle = "Weekly";
            $datas = $datas->addSelect(\DB::raw('DATE_FORMAT(created_at, "%U") as month'));
        } else if ($request->dwm == 'Monthly') {
            $subtitle = "Monthly";
            $datas = $datas->addSelect(\DB::raw('DATE_FORMAT(created_at, "%b") as month'));
        } else {
            $datas = $datas->addSelect(\DB::raw('DATE_FORMAT(created_at, "%b") as month'));
        }
        /* if($request->year)
        {
            $datas->whereYear('users.created_at', $request->year);
        }else
        {
            $datas = $datas->where('users.created_at', '>=', Carbon::now()->subYear());
        }*/
        //dd($datas->groupBy('month')->get()->pluck('count', 'month')->toArray());
        //$datas = $datas->groupBy('month')->orderBy('created_at', 'asc')->get()->pluck('count', 'month')->toArray();
        $datas = $datas->groupBy('month')->get()->pluck('count', 'month')->toArray();
        return view('admin.edsauser.grid', compact('users', 'datas', 'dataProvider', 'title', 'subtitle'));
    }

    public function UpdateMinimumBalance(Request $request){
        // Assuming you want to update the authenticated user's minimum_balance
     // Find the user with ID 2
     $user = User::find(2);  // Or $request->user_id if you want dynamic user ID
     // Check if the user exists
     if (!$user) {
         return redirect()->back()->with('error', 'User not found!');
     }
 
     // Validate the incoming minimum_balance
     $request->validate([
         'minimum_balance' => 'required',
     ]);
     // Update the minimum_balance for the found user
      // Try updating the user's minimum_balance
    
    User::where('id',$user->id)->update(['edsa_min_balance'=>$request->minimum_balance]);
        

 
    // Redirect back with a success message
    return redirect()->back()->with('success', 'Minimum Balance updated successfully!');
    }


    public function applyFilters($request)
    {

        !$request->name || $this->users->orWhere('users.name', 'like', "%{$request->name}%");

        !$request->mobile_number || $this->users->orWhere('users.mobile_number', 'like', "%{$request->mobile_number}%");

        !$request->email || $this->users->orWhere('users.email', 'like', "%{$request->email}%");
    }
}