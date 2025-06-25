<?php


namespace App\Http\Controllers\Admin;


use App\Models\Address;
use App\Models\AddressSection;
use Illuminate\Http\Request;

class AddressSectionsController extends AdminController
{
    public function index()
    {

        $addressSection = AddressSection::with('address')->latest()->paginate();

        return view('admin.address-section.grid', compact('addressSection'));
    }

    public function create()
    {
        if ($this->guard()->user()->hasRole('admin')) {

            $area = Address::pluck('ward_number', 'id');
            $addressArea = new AddressSection();
            return view('admin.address-section.edit', compact('area', 'addressArea'));
        }
        abort(403, 'User does not have the right roles.');
    }

    public function store(Request $request)
    {


        $this->validate($request, [
            'name' => ['required', 'unique:address_sections,name'],
        ]);

        $values = $request->only('name');

        $area = new AddressSection($values);
        $area->address()->associate(Address::findOrFail($request->address_id));
        $area->save();

        return redirect()->route('admin.address-section.show', ['id' => $area->id])->with($this->setMessage('Area details successfully updated.'));
    }

    public function show($id)
    {
        $addressArea = AddressSection::with('address')->findOrFail($id);
        return view('admin.address-section.show', compact('addressArea'));
    }

    public function edit($id)
    {

        $area = Address::pluck('ward_number', 'id');
        $addressArea = AddressSection::with('address')->findOrFail($id);
        return view('admin.address-section.edit', compact('addressArea', 'area'));
    }

    public function update(Request $request, $id)
    {

        try {

            $this->validate($request, [
                'name' => ['required'],
            ]);

            $area = AddressSection::findOrFail($id);

            $area->name = $request->name;

            $area->address()->associate(Address::findOrFail($request->address_id));

            $area->save();

            return redirect()->route('admin.address-section.index')->with($this->setMessage('Area details successfully updated.'));
        } catch (\Illuminate\Database\QueryException $ex) {
            return redirect()->back()->withErrors(['message1' => "!Oops, Something goes wrong."]);
        }
    }

    public function destroy($id)
    {
        if ($this->guard()->user()->hasRole('admin')) {
            $address = AddressSection::findOrFail($id);
            $address->delete();
            return redirect()->route('admin.address-section.index')->with($this->setMessage('Area details successfully updated.'));
        }
        abort(403, 'User does not have the right roles.');
    }
}
