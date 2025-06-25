<?php

namespace App\Http\Controllers\Admin;


use App\Exports\UsersExport;
use App\Grids\UsersGrid;
use App\Models\ResidentialCategory;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserProfile;
use App\Models\SellerCategory;
use App\Models\ProductCategory;
use App\Notifications\Admin\User\UserVerifiedNotification;
use DataTables;
use Grids;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Woo\GridView\DataProviders\EloquentDataProvider;

use App\Models\SellerDetail;

use App\SellerTheme;

use App\SellerThemeRelation;

class UserController extends AdminController
{
    protected $users;
    protected $digitalAddress;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = "Last One Year Digital Address Report";
        $subtitle = "Monthly";

        $this->users = User::where('is_seller', 0)
        ->orderBy('created_at', 'desc');

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
        return view('admin.user.grid', compact('users', 'datas', 'dataProvider', 'title', 'subtitle'));
    }

    public function applyFilters($request)
    {

        !$request->name || $this->users->orWhere('users.name', 'like', "%{$request->name}%");

        !$request->mobile_number || $this->users->orWhere('users.mobile_number', 'like', "%{$request->mobile_number}%");

        !$request->email || $this->users->orWhere('users.email', 'like', "%{$request->email}%");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        // $user = User::findOrFail($id);
         $user = User::with('seller_detail')->where('id',$id)->first();
        //  dd($user->seller_detail);
         $user_id = $user->seller_detail->user_id;
         $category = SellerCategory::where('category_id',$user->seller_detail->store_category)->first();
        //  dd($category);
        if (isset($category->category_id)) {
            # code...
            
          $product_category=ProductCategory::where('id',$category->category_id)->first();
        }else{
        $product_category = 'N/A';

        }
       
        if (isset($category->plan_id)) {
            # code...
            
         
            $planName = Plan::where('id',$category->plan_id)->first();
        }else{
            $planName = 'N/A';

        }

       
        $this->digitalAddress = $user->digitalAddresses()->with('user', 'address', 'addressArea', 'addressChiefdom', 'addressSection')->orderBy('created_at', 'desc');
        $this->applyFiltersDigitalAddress($request);
        $digitalAddress = $this->digitalAddress->paginate();

        //dd(DigitalAddress::query()->where('user_id',$id));
        /* return view('admin.user.show',[
                'dataProvider' => new EloquentDataProvider(DigitalAddress::query()->with('address','address.addressArea','address.addressChiefdom','address.addressSection','addressArea','addressChiefdom','addressSection')->where('user_id',$id)),
                'user' =>$user
            ] );*/

            $sellerThemes = SellerTheme::all();
            $specific_seller_theme = SellerThemeRelation::with('theme')->where('seller_id','=',$user_id)->first();
            if (isset($specific_seller_theme->theme)) {
                $theme_name = $specific_seller_theme->theme;
            }else{
                $theme_name = 'N/A';
            }

        return view('admin.user.show', [
            'digitalAddresses' => $digitalAddress,
            'user' => $user,
            'plan' => $product_category,
            'plan_name' =>$planName,
            'sellerThemes'=>$sellerThemes,
            'specific_theme'=>$theme_name
        ]);
    }

    public function showUser($id, Request $request)
    {
        // $user = User::findOrFail($id);
         $user = User::where('id',$id)->first();
      
    

       
        $this->digitalAddress = $user->digitalAddresses()->with('user', 'address', 'addressArea', 'addressChiefdom', 'addressSection')->orderBy('created_at', 'desc');
        $this->applyFiltersDigitalAddress($request);
        $digitalAddress = $this->digitalAddress->paginate();

        //dd(DigitalAddress::query()->where('user_id',$id));
        /* return view('admin.user.show',[
                'dataProvider' => new EloquentDataProvider(DigitalAddress::query()->with('address','address.addressArea','address.addressChiefdom','address.addressSection','addressArea','addressChiefdom','addressSection')->where('user_id',$id)),
                'user' =>$user
            ] );*/


        return view('admin.user.showUser', [
            'digitalAddresses' => $digitalAddress,
            'user' => $user
        ]);
    }

    public function uploadBusinessRegImage(Request $request)
        {
            // Validate the request
            $request->validate([
                'seller_id' => 'required|exists:users,id', // Ensure seller_id exists in users table
                'business_reg_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Ensure it's an image
            ]);

            try {
                // Handle file upload
                $filePath = null;
                if ($request->hasFile('business_reg_image')) {
                    $filePath = $request->file('business_reg_image')->store('seller_category', 'public');
                }

                // Update or create the record in the SellerDetail table
                SellerDetail::updateOrCreate(
                    ['user_id' => $request->seller_id],
                    ['business_registration_image' => $filePath]
                );

                return redirect()->back()->with('success', 'Business Registration Image uploaded successfully!');
            } catch (\Exception $e) {
                \Log::error('Upload Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload the Business Registration Image.');
            }
        }

        public function UpdateLogo(Request $request)
    {
       
        $request->validate([
            'business_logo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Ensure it's an image
        ]);
            // dd($request->all());
        try {
           // Retrieve the seller detail record by user_id
            $sellerDetail = SellerDetail::where('user_id', $request->seller_id)->first();

            if (!$sellerDetail) {
                return redirect()->back()->with('error', 'Seller details not found.');
            }

            // Handle the file upload
            if ($request->hasFile('business_logo')) {
                $file = $request->file('business_logo');
                $filename = time() . '_' . $file->getClientOriginalName();

                // Save the file to the public directory
                $file->move(public_path('busniess_images'), $filename);

                // Update the seller_detail record with the new logo path
                $sellerDetail->business_logo = $filename;
                $sellerDetail->save();
            }

            return redirect()->back()->with('success', 'Business Logo Image uploaded successfully!');
        } catch (\Exception $e) {
            \Log::error('Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to upload the Business Registration Image.');
        }
    }


    public function addSellerAdditionalDesc(Request $request){
        $sellerdetail = SellerDetail::where('user_id',$request->seller_id)->update([
            'additional_information' => $request->addition_desc
        ]);
        return redirect()->back()->with('success', 'Additional info added successfully.');
       
    }
    public function updateSellerBusinessdetails(Request $request){
        $sellerdetail = SellerDetail::where('user_id',$request->user_id)->update([
            'business_name' => $request->business_name,
                'tin' => $request->tin,
                'street_number' => $request->street_number,
                'street_name' => $request->street_name,
                'area' => $request->area,
                'ward' => $request->ward,
                'section' => $request->section,
                'chiefdon' => $request->chiefdon,
                'province' => $request->province,
                'business_coordinates' => $request->business_coordinates,
                'mobile1' => $request->mobile1,
                'mobile2' => $request->mobile2,
                'mobile3' => $request->mobile3,
                'business_email' => $request->business_email,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time
        ]);
        return redirect()->back()->with('success', 'seller info added successfully.');
       
    }



    public function assignTheme(Request $request)
    {
        // Validate the form inputs
        $request->validate([
            'seller_id' => 'required',
            'theme_id' => 'required',
        ]);

        SellerThemeRelation::updateOrCreate(
            [
                'seller_id' => $request->seller_id, // Search for a record with this seller_id
            ],
            [
                'theme_id' => $request->theme_id,   // If found, update the theme_id
            ]
        );

        return redirect()->back()->with('success', 'Theme has been assigned successfully.');
    }

    public function DeletassignTheme(Request $request)
    {
        // Validate the incoming request (e.g., seller_id and theme_id)
        $request->validate([
            'seller_id' => 'required',
            'theme_id' => 'required', // Assuming `themes` table exists and has an `id` column
        ]);

        // Find the relation between seller and theme and delete it
        $sellerThemeRelation = SellerThemeRelation::where('seller_id', $request->seller_id)
            ->where('theme_id', $request->theme_id)
            ->first();
            // dd($sellerThemeRelation);

        if ($sellerThemeRelation) {
            $sellerThemeRelation->delete();

            return redirect()->back()->with('success', 'Theme has been deleted successfully.');
        }

        return redirect()->back()->with('success', 'No Theme found.');
    }

    public function applyFiltersDigitalAddress($request)
    {
        !$request->area || $this->digitalAddress->whereHas('addressArea', function ($query) use ($request) {
            return $query->where('name', 'like', "%{$request->area}%");
        });

        !$request->chiefdom || $this->digitalAddress->whereHas('addressChiefdom', function ($query) use ($request) {
            return $query->where('name', 'like', "%{$request->chiefdom}%");
        });

        !$request->section || $this->digitalAddress->whereHas('addressSection', function ($query) use ($request) {
            return $query->where('name', 'like', "%{$request->section}%");
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->guard()->user()->hasRole('admin')) {
            $user = User::findOrFail($id);

            return view('admin.user.edit', compact('user'));
        }
        abort(403, 'User does not have the right roles.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($this->guard()->user()->hasRole('admin')) {
            $user = User::findOrFail($id);

            $rules = [
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required'],
                'is_active' => ['nullable']
            ];


            $data = $request->only([
                'name',
                'email',
                'is_active',
                'is_edsa_agent',
                'is_dstv_agent',
                'edsa_stocks'
            ]);

            $user->update($data);

            return redirect()->route('admin.user.show',  $id)->with($this->setMessage('User successfully updated.', self::MESSAGE_SUCCESS));;
        }
        abort(403, 'User does not have the right roles.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
