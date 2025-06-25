<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Models\AddressArea;
use App\Models\AddressChiefdom;
use App\Models\AddressSection;
use App\Models\DigitalAddress;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenLocationCode\OpenLocationCode;

class DigitalAddressController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected function getDigitalAddresses()
    {

        return request()->user()->digitalAddresses()->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')->paginate();
    }


    public function index(Request $request)
    {
        $queryString = $request->input('q');


        $digitalAddresses = $request->user()->digitalAddresses()->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection');

        if ($queryString) {
            $digitalAddresses->where(function ($query) use ($queryString) {

                $query->where('digital_addresses', 'LIKE', "%$queryString%")
                    ->orWhere('type', 'LIKE', "%$queryString%")
                    ->orWhereHas('addressArea', function ($q) use ($queryString) {
                        $q->where('name', 'LIKE', "%$queryString%");
                    })
                    ->orWhereHas('addressChiefdom', function ($q) use ($queryString) {
                        $q->where('name', 'LIKE', "%$queryString%");
                    })
                    ->orWhereHas('addressSection', function ($q) use ($queryString) {
                        $q->where('name', 'LIKE', "%$queryString%");
                    });
            });
        }

        return $this->genericSuccess($digitalAddresses->paginate());
    }

    /**
     * Search in data.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $queryString = $request->searchTerm;
        DB::enableQueryLog();
        $result = request()->user()->digitalAddresses()
            ->with('address', 'addressArea', 'addressChiefdom', 'addressSection')
            ->where('digital_addresses', 'LIKE', "%$queryString%")
            ->orWhereHas('addressArea', function ($q) use ($queryString) {
                $q->where('name', 'LIKE', "%$queryString%");
            })
            ->orWhereHas('addressChiefdom', function ($q) use ($queryString) {
                $q->where('name', 'LIKE', "%$queryString%");
            })
            ->orWhereHas('addressSection', function ($q) use ($queryString) {
                $q->where('name', 'LIKE', "%$queryString%");
            })
            ->get();

        return $this->genericSuccess($result);
    }

    /**
     * Search in data.
     *
     * @return \Illuminate\Http\Response
     */
    public function areaSearch(Request $request)
    {

        $queryString = $request->searchTerm;

        $digitalAddress = AddressArea::where('name', 'LIKE', "%$queryString%")->with('addressSection', 'address', 'address.addressChiefdom', 'address.addressSection')->limit(20)->get();

        return $this->genericSuccess($digitalAddress);
    }

    /**
     * Search in data.
     *
     * @return \Illuminate\Http\Response
     */
    public function areaById(Request $request)
    {
        $digitalAddress = Address::with(['chiefdoms:id,area_id,chiefdom', 'sections:id,area_id,section'])->limit(20)->select('id')->findOrFail($request->id);

        return $this->genericSuccess($digitalAddress);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'address_area_id' => [
                'required',
                Rule::exists('address_areas', 'id')->where('address_id', $request->input('address_id'))
            ],
            'address_chiefdom_id' => [
                'required',
                Rule::exists('address_chiefdoms', 'id')->where('address_id', $request->input('address_id'))
            ],
            'address_section_id' => [
                'required',
                Rule::exists('address_sections', 'id')->where('address_id', $request->input('address_id'))
            ],
            'digital_addresses' => 'required|string|max:50',
            'type' => 'required|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $address = Address::find($request->address_id);
        $addressesArea = AddressArea::find($request->address_area_id);
        $addressesChiefdom = AddressChiefdom::find($request->address_chiefdom_id);
        $addressesSection = AddressSection::find($request->address_section_id);

        $user = $request->user();

        $digitalAddress = new DigitalAddress();
        $digitalAddress->digital_addresses = $request->digital_addresses;
        $digitalAddress->open_location_code = OpenLocationCode::encode($request->latitude, $request->longitude);
        $digitalAddress->type = $request->type;
        $digitalAddress->latitude = $request->latitude;
        $digitalAddress->longitude = $request->longitude;

        $digitalAddress->user()->associate($user);
        $digitalAddress->address()->associate($address);
        $digitalAddress->addressArea()->associate($addressesArea);
        $digitalAddress->addressChiefdom()->associate($addressesChiefdom);
        $digitalAddress->addressSection()->associate($addressesSection);
        $digitalAddress->save();
        //$digitalAddress->digital_addresses = $digitalAddress->digital_addresses . "-" . $digitalAddress->id;
        $digitalAddress->save();
        //$digitalAddress = $user->digitalAddresses()->create($request->all());

        return $this->success('Digital Address successfully created.', [
            'items' => $this->getDigitalAddresses()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DigitalAddresses $digitalAddresses
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {

        $digitalAddress = $request->user()->digitalAddresses()->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')->findOrFail($id);

        return $this->genericSuccess($digitalAddress);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\DigitalAddresses $digitalAddresses
     * @return \Illuminate\Http\Response
     */
    public function edit(DigitalAddresses $digitalAddresses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\DigitalAddresses $digitalAddresses
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'address_area_id' => [
                'required',
                Rule::exists('address_areas', 'id')->where('address_id', $request->input('address_id'))
            ],
            'address_chiefdom_id' => [
                'required',
                Rule::exists('address_chiefdoms', 'id')->where('address_id', $request->input('address_id'))
            ],
            'address_section_id' => [
                'required',
                Rule::exists('address_sections', 'id')->where('address_id', $request->input('address_id'))
            ],
            'digital_addresses' => 'required|string|max:50',
            'type' => 'required|string|max:191',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $digitalAddress = $request->user()->digitalAddresses()->find($id);

        $digitalAddress->fill($request->all());

        $digitalAddress->open_location_code = OpenLocationCode::encode($request->latitude, $request->longitude);
        $digitalAddress->address()->associate($request->input('address_id'));
        $digitalAddress->addressArea()->associate($request->input('address_area_id'));
        $digitalAddress->addressChiefdom()->associate($request->input('address_chiefdom_id'));
        $digitalAddress->addressSection()->associate($request->input('address_section_id'));
        //$digitalAddress->digital_addresses = $digitalAddress->digital_addresses;
        $digitalAddress->save();

        return $this->success('Digital Address successfully updated.', [
            'items' => $this->getDigitalAddresses()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DigitalAddresses $digitalAddresses
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $digitalAddress = DigitalAddress::findOrFail($id);
        $digitalAddress->delete();

        return $this->success('Digital Address successfully deleted.', [
            'items' => $this->getDigitalAddresses()
        ]);
    }
}
