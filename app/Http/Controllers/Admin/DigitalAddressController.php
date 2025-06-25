<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DigitalAddressExport;
use App\Models\DigitalAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

//use Excel;

class DigitalAddressController extends AdminController
{
    protected $digitalAddress;

    public function index(Request $request)
    {

        $title = "Last One Year Digital Address Report";
        $subtitle = "Monthly";

        $this->digitalAddress = DigitalAddress::with('user', 'address', 'addressArea', 'addressChiefdom', 'addressSection')
            ->latest();

        $this->applyFilters($request);

        $digitalAddress = $this->digitalAddress->paginate();

        if ($request->download) {
            return Excel::download(new DigitalAddressExport($request), 'digital_addresses.xlsx');
        }


        $datas = DigitalAddress::select(\DB::raw('COUNT(id) as count'));

        if ($request->dwm == 'Daily') {

            $subtitle = "Daily";
            $datas->addSelect(DB::raw('DATE_FORMAT(created_at, "%D %b") as month'));
        } else if ($request->dwm == 'Weekly') {
            $subtitle = "Weekly";
            $datas->addSelect(DB::raw('DATE_FORMAT(created_at, "%U") as month'));
        } else if ($request->dwm == 'Monthly') {
            $subtitle = "Monthly";
            $datas->addSelect(DB::raw('DATE_FORMAT(created_at, "%b") as month'));
        } else {
            $datas->addSelect(DB::raw('DATE_FORMAT(created_at, "%b") as month'));
        }


        if ($request->area) {
            $datas->whereHas('addressArea', function ($query) use ($request) {
                return $query->where('name', 'like', "%{$request->area}%");
            });
        }
        if ($request->chiefdom) {
            $datas->whereHas('addressChiefdom', function ($query) use ($request) {
                return $query->where('name', 'like', "%{$request->chiefdom}%");
            });
        }
        if ($request->section) {
            $datas->whereHas('addressSection', function ($query) use ($request) {
                return $query->where('name', 'like', "%{$request->section}%");
            });
        }

        /*if($request->year)
        {
            $datas = $datas->whereYear('created_at', $request->year);
        }else
        {
            $datas = $datas->where('created_at', '>=', Carbon::now()->subYear());
        }*/

        $datas = $datas->groupBy('month')->pluck('count', 'month')->toArray();

        return view('admin.digital.list', compact('digitalAddress', 'datas', 'title', 'subtitle'));
    }

    public function show($id)
    {
        $digitalAddresses = DigitalAddress::with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')->findOrFail($id);

        return view('admin.digital.show', [
            'digitalAddresses' => $digitalAddresses
        ]);

    }

    public function applyFilters($request)
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

        !$request->year || $this->digitalAddress->whereYear('digital_addresses.created_at', $request->year);


    }

}
