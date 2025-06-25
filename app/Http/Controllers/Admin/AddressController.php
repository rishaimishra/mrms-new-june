<?php


namespace App\Http\Controllers\Admin;


use App\Models\Address;
use App\Models\AddressArea;
use App\Models\AddressChiefdom;
use App\Models\AddressSection;
use Illuminate\Http\Request;

class AddressController extends AdminController
{
    public function index()
    {

        $areas = Address::with('addressArea', 'addressChiefdom', 'addressSection')->latest()->paginate();

        return view('admin.area.grid', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->guard()->user()->hasRole('admin')) {
            $area = new Address();
            $addressArea = AddressArea::pluck('name', 'id');
            $addressChiefdom = AddressChiefdom::pluck('name', 'id');
            $addressSection = AddressSection::pluck('name', 'id');

            /*Query */
            //$area = Address::with('addressArea','addressChiefdom','addressSection')->get();
            /*End*/

            return view('admin.area.edit', compact('area', 'addressArea', 'addressChiefdom', 'addressSection'));
        }
        abort(403, 'User does not have the right roles.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->guard()->user()->hasRole('admin')) {
            $this->validate($request, [
                'ward_number' => ['required', 'unique:addresses,ward_number', 'numeric'],
                'constituency' => 'required|string|max:191',
                'chiefdoms' => 'nullable|array',
                'sections' => 'nullable|array',
                'area_names' => 'nullable|array',
                'district' => 'required|string|max:191',
                'province' => 'required|string|max:191'
            ]);

            $values = $request->only(
                'ward_number',
                'constituency',
                'district',
                'province'
            );

            $area = new Address($values);
            $area->save();
            foreach (array_filter($request->chiefdoms) as $chiefdom) {
                $area->addressChiefdom()->firstOrCreate(['name' => $chiefdom]);
            }
            $area->addressChiefdom()->whereNotIn('name', array_filter($request->chiefdoms))->delete();

            foreach (array_filter($request->sections) as $section) {
                $area->addressSection()->firstOrCreate(['name' => $section]);
            }
            $area->addressSection()->whereNotIn('name', array_filter($request->sections))->delete();
            foreach (array_filter($request->area_names) as $area_name) {
                $area->addressArea()->firstOrCreate(['name' => $area_name]);
            }
            $area->addressArea()->whereNotIn('name', array_filter($request->area_names))->delete();

            return redirect()->route('admin.address.show', ['id' => $area->id])->with($this->setMessage('Area details successfully updated.'));
        }
        abort(403, 'User does not have the right roles.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $area = Address::with('addressArea', 'addressChiefdom', 'addressSection')->findOrFail($id);

        return view('admin.area.show', compact('area'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->guard()->user()->hasRole('admin')) {

            $area = Address::findOrFail($id);
            $addressArea = AddressArea::pluck('name', 'id');
            $addressChiefdom = AddressChiefdom::pluck('name', 'id');
            $addressSection = AddressSection::pluck('name', 'id');

            return view('admin.area.edit', compact('area', 'addressArea', 'addressChiefdom', 'addressSection'));
        }
        abort(403, 'User does not have the right roles.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($this->guard()->user()->hasRole('admin')) {

            try {
                $this->validate($request, [
                    'ward_number' => 'required',
                    'constituency' => 'required',
                    'chiefdoms' => 'nullable|array',
                    'sections' => 'nullable|array',
                    'area_names' => 'nullable|array',
                    'district' => 'required',
                    'province' => 'required'
                ]);

                $values = $request->only(
                    'ward_number',
                    'constituency',
                    'district',
                    'province'
                );

                $area = Address::findOrFail($id);
                $area->ward_number = $request->ward_number;
                $area->constituency = $request->constituency;
                $area->district = $request->district;
                $area->province = $request->province;
                $area->save();

                foreach (array_filter($request->chiefdoms) as $chiefdom) {
                    $area->addressChiefdom()->firstOrCreate(['name' => $chiefdom]);
                }
                $area->addressChiefdom()->whereNotIn('name', array_filter($request->chiefdoms))->delete();

                foreach (array_filter($request->sections) as $section) {
                    $area->addressSection()->firstOrCreate(['name' => $section]);
                }
                $area->addressSection()->whereNotIn('name', array_filter($request->sections))->delete();
                foreach (array_filter($request->area_names) as $area_name) {
                    $area->addressArea()->firstOrCreate(['name' => $area_name]);
                }
                $area->addressArea()->whereNotIn('name', array_filter($request->area_names))->delete();

                return redirect()->route('admin.address.show', ['id' => $id])->with($this->setMessage('Area details successfully updated.'));
            } catch (\Illuminate\Database\QueryException $ex) {
                return redirect()->back()->withErrors(['message1' => "!Oops, Something goes wrong."]);
                //dd($ex->getMessage());
                // Note any method of class PDOException can be called on $ex.
            }
        }
        abort(403, 'User does not have the right roles.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->guard()->user()->hasRole('admin')) {
            $address = Address::findOrFail($id);
            $address->delete();
            return redirect()->route('admin.address.index')->with($this->setMessage('Area details successfully updated.'));
        }
        abort(403, 'User does not have the right roles.');
    }
}
