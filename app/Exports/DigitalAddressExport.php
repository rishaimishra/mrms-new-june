<?php

namespace App\Exports;

use App\Models\DigitalAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class DigitalAddressExport implements FromCollection
{
    protected $digitalAddress;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(Request $request)
    {
        $this->digitalAddress = DigitalAddress::with('user', 'address', 'addressArea', 'addressChiefdom', 'addressSection')->orderBy('created_at', 'desc');
        $this->applyFilters($request);
    }

    public function collection()
    {
        $digitalAddresses = $this->digitalAddress->get()->toArray();
        //dd($digitalAddresses);
        $addressArray[] = ['User', 'Area', 'Ward', 'Constituency', 'Chiefdom', 'Section', 'district', 'Province', 'Tag', 'Digital Addresses', 'Latitude', 'Longitude', 'Created at'];
        foreach ($digitalAddresses as $key => $value) {

            $addressArray[] = [
                $value['user']['name'],
                $value['address_area']['name'],
                $value['address']['ward_number'],
                $value['address']['constituency'],
                (($value['address_chiefdom']) ? ($value['address_chiefdom']['name']) : ""),
                $value['address_section']['name'],
                $value['address']['district'],
                $value['address']['province'],
                $value['type'],
                $value['digital_addresses'],
                $value['latitude'],
                $value['longitude'],
                $value['created_at']
            ];
        }

        return new Collection($addressArray);
        //return $this->digitalAddress->get();
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
