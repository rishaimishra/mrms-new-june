<?php


namespace App\Http\Controllers\Admin;


use App\Library\Grid\Grid;
use App\Models\Address;
use App\Models\AddressArea;
use App\Models\AddressSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class AddressAreasController extends AdminController
{
    public function index()
    {
        $grid = (new Grid())
            ->setQuery(AddressArea::with(['address', 'addressSection'])->latest())
            ->setColumns([
                [
                    'field' => 'name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'address_section',
                    'label' => 'Section',
                    //'sortable' => true,
                    'filterable' => [
                        'callback' => function ($query, $value) {
                            $query->whereHas('addressSection', function ($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],
                    'formatter' => function ($field, AddressArea $addressArea) {
                        return $addressArea->addressSection->name;
                    }
                ],
                [
                    'field' => 'address_ward_number',
                    'label' => 'Ward Number',
                    //'sortable' => true,
                    'filterable' => [
                        'callback' => function ($query, $value) {
                            $query->whereHas('address', function ($query) use ($value) {
                                $query->where('ward_number', $value);
                            });
                        },
                    ],
                    'formatter' => function ($field, AddressArea $addressArea) {
                        return $addressArea->address->ward_number;
                    }
                ],
                [
                    'field' => 'created_at',
                    'label' => 'Created At',
                    'sortable' => true
                ],
                [
                    'field' => 'updated_at',
                    'label' => 'Updated At',
                    'sortable' => true
                ]
            ])->setButtons([
                [
                    'label' => 'Edit',
                    'icon' => 'edit',
                    'url' => function ($item) {
                        return route('admin.address-area.edit', $item->id);
                    }
                ]

            ])->generate();

        return view('admin.address-area.grid', compact('grid'));
    }

    public function create()
    {

        if (request()->user()->hasRole('admin')) {

            $area = Address::pluck('ward_number', 'id');
            $addressArea = new AddressArea();
            $addressSection = AddressSection::where('address_id', 1)->pluck('name', 'id')->prepend('Select section', '');

            return view('admin.address-area.edit', compact('area', 'addressArea', 'addressSection'));
        }

        throw PermissionDoesNotExist::create('');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'address_id' => 'required|exists:addresses,id',
            'address_section_id' => [
                'required',
                Rule::exists('address_sections', 'id')
                    ->where('address_id', $request->input('address_id'))
            ],
        ]);


        $values = $request->only('name');

        $area = new AddressArea($values);

        $area->address()->associate($request->input('address_id'));
        $area->addressSection()->associate($request->input('address_section_id'));
        $area->save();

        return redirect()->route('admin.address-area.index')
            ->with($this->setMessage('Area details successfully updated.'));
    }

    public function show($id)
    {

        $addressArea = AddressArea::with('address')->findOrFail($id);

        return view('admin.address-area.show', compact('addressArea'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $area = Address::pluck('ward_number', 'id');
        /*Section base*/
        //$addressArea = AddressSection::with('address','addressArea','address.addressSection')->findOrFail($id);
        //$addressSection = $addressArea->address->addressSection()->pluck('name','id');

        /*Area base*/
        $addressArea = AddressArea::with('address', 'address.addressSection', 'addressSection')->findOrFail($id);

        $addressSection = $addressArea->address->addressSection()->pluck('name', 'id')->prepend('Select section', '');

        return view('admin.address-area.edit', compact('addressArea', 'area', 'addressSection'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'address_id' => 'required|exists:addresses,id',
            'address_section_id' => [
                'required',
                Rule::exists('address_sections', 'id')->where('address_id', $request->input('address_id'))
            ],
        ]);

        $area = AddressArea::findOrFail($id);
        $area->name = $request->name;

        $area->address()->associate($request->address_id);
        $area->addressSection()->associate($request->address_section_id);
        $area->save();

        return redirect()->route('admin.address-area.index')->with($this->setMessage('Area details successfully updated.'));
    }

    public function destroy($id)
    {
        if (\request()->user()->hasRole('admin')) {
            $address = AddressArea::findOrFail($id);
            $address->delete();
            return redirect()->route('admin.address-area.index')->with($this->setMessage('Area details successfully updated.'));
        }
        abort(403, 'User does not have the right roles.');
    }

    public function getSection($id)
    {
        $addressSection = AddressSection::where('address_id', $id)->pluck('name', 'id');
        return response()->json($addressSection);
    }
}
