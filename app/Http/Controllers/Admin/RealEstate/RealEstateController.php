<?php

namespace App\Http\Controllers\Admin\RealEstate;

use App\Http\Controllers\Admin\AdminController;
use App\Library\Grid\Grid;
use App\Models\Auto;
use App\Models\RealEstate;
use App\Models\RealEstateCategory;
use App\Models\RealEstateImage;
use App\Models\User;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RealEstateController extends AdminController
{
    const ENTITY_ID = 2;

    public function index()
    {
        $query = RealEstate::with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')->latest();

        $grid = (new Grid())
            ->setQuery($query)
            ->setColumns([
                [
                    'field' => 'name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'category',
                    'label' => 'Category',
                    //'sortable' => true,
                    /*'filterable' => [
                        'callback' => function($query, $value) {
                            $query->whereHas('addressSection', function($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],*/
                    'formatter' => function ($field, RealEstate $RealEstate) {
                        return $RealEstate->categories()->pluck('name')->implode(', ');
                    }
                ],
                [
                    'field' => 'is_available',
                    'label' => 'Is Available ',
                    'sortable' => true,
                    'formatter' => function ($field, RealEstate $RealEstate) {
                        return $RealEstate->is_available ? "Yes" : "No";
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
                    'icon' => 'remove_red_eye',
                    'url' => function ($item) {
                        return route('admin.real-estate.edit', $item->id);
                    }
                ],
                [
                    'label' => 'Interested user',
                    'icon' => 'verified_user',
                    'url' => function ($item) {
                        return route('admin.real-estate.interested.users', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.realestate.grid', compact('grid'));
    }

    public function create()
    {
        $categories = RealEstateCategory::get();
        $realEstate = new RealEstate();

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        return view('admin.realestate.edit', compact('realEstate', 'categories', 'attributeSets'));
    }

    protected function getAttributes(RealEstate $realEstate)
    {
        $attributeSet = AttributeSet::find($realEstate->attribute_set_id);

        if (! $attributeSet) {
            return false;
        }

        return $attributeSet->groups()->whereHas('attributes')->with('attributes')->get();
    }

    protected function validator($request, $additionalRules = [], $isUpdate = false)
    {
        return validator($request->all(), [
                'title' => 'nullable',
                'name' => 'required',
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
                'sequence' => 'nullable|numeric|min:1|max:9999',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'categories' => 'required|array',
                'categories.*' => 'required|numeric',
                'images' => ($isUpdate ? 'nullable' : 'required') . '|array',
                'images.*' => 'required|file|mimes:jpg,jpeg,png'
            ] + $additionalRules);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request, [
            'attribute_set_id' => ['required']
        ])->validate();

        $digital_addresses = $this->generateDigitalAddress($request); //EP002 24.585188 73.709946

        $realEstate = new RealEstate();

        $realEstate->entity_id = self::ENTITY_ID;

        $realEstate->digital_addresses = $digital_addresses ?? 0;
        $realEstate->title = $request->input('title');
        $realEstate->name = $request->input('name');
        $realEstate->about = $request->input('about', '');
        $realEstate->type = RealEstate::TYPE_PLACE;
        $realEstate->map_addresses = $request->input('address', '');
        $realEstate->latitude = $request->input('latitude');
        $realEstate->longitude = $request->input('longitude');
        $realEstate->availability_times = json_decode($request->input('availability_times', ''));
        $realEstate->meta_tag1 = $request->input('meta_tag1', '');
        $realEstate->meta_tag2 = $request->input('meta_tag2', '');
        $realEstate->meta_tag3 = $request->input('meta_tag3', '');
        $realEstate->sequence = $request->input('sequence', '');
        $realEstate->is_available = $request->is_available;
        $realEstate->address()->associate($request->input('address_id'));
        $realEstate->addressArea()->associate($request->input('address_area_id'));
        $realEstate->addressChiefdom()->associate($request->input('address_chiefdom_id'));
        $realEstate->addressSection()->associate($request->input('address_section_id'));

        $realEstate->fill([
            'entity_id' => self::ENTITY_ID,
            'attribute_set_id' => $request->attribute_set_id
        ]);

        $request->user()->places()->save($realEstate);

        $realEstate->categories()->sync($request->categories);

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload branch images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('place', $image);
                $path = $this->resizeImage($image, 'realstate', 800);
                $realEstate->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.real-estate.index')->with($this->setMessage('Real Estate Saved Successfully', self::MESSAGE_SUCCESS));
    }

    public function show(RealEstate $realEstate)
    {
        //
    }

    public function edit(RealEstate $realEstate)
    {
        $realEstate = RealEstate::select(['*', 'attr.*'])->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')
            ->find($realEstate->id);

        $categories = RealEstateCategory::get();

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        $attributeGroups = $this->getAttributes($realEstate);

        return view('admin.realestate.edit', compact('categories', 'realEstate', 'attributeGroups', 'attributeSets'));
    }

    public function update(Request $request, RealEstate $realEstate)
    {
        
        if ($request->input('attribute_set_id')) {
            $realEstate->attribute_set_id = $request->input('attribute_set_id');
            $realEstate->save();
        }

        $attributeGroups = $this->getAttributes($realEstate);

        $additionalRules = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
            return $attributeGroup->attributes->map(function (Attribute $attribute) {
                return [
                    $attribute->attribute_code => [$attribute->is_required ? 'required' : 'nullable', 'string']
                ];
            });
        })->toArray();

        $this->validator($request, $additionalRules, true)->validate();

        $attributeValues = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
            return $attributeGroup->attributes->pluck('attribute_code')->map(function ($code) {
                return [$code => \request($code)];
            });
        })->toArray();

        $digitalAddresses = $this->generateDigitalAddress($request); //EP002 24.585188 73.709946

        $realEstate->digital_addresses = $digitalAddresses ?? 0;
        $realEstate->title = $request->input('title');
        $realEstate->name = $request->name;
        $realEstate->about = $request->input('about', '');
        $realEstate->type = RealEstate::TYPE_PLACE;
        $realEstate->map_addresses = $request->address ?? '';
        $realEstate->latitude = $request->latitude;
        $realEstate->longitude = $request->longitude;
        $realEstate->availability_times = json_decode(stripslashes($request->input('availability_times', '')));
        $realEstate->meta_tag1 = $request->input('meta_tag1', '');
        $realEstate->meta_tag2 = $request->input('meta_tag2', '');
        $realEstate->meta_tag3 = $request->input('meta_tag3', '');
        $realEstate->sequence = $request->input('sequence', '');
        $realEstate->is_available = $request->is_available;
        $realEstate->address()->associate($request->address_id);
        $realEstate->addressArea()->associate($request->address_area_id);
        $realEstate->addressChiefdom()->associate($request->address_chiefdom_id);
        $realEstate->addressSection()->associate($request->address_section_id);

        foreach ($attributeValues as $attr) {

            $field = array_keys($attr)[0];
            $val = $attr[$field];

            $realEstate->{$field} = $val;
        }

        $realEstate->save();

        $realEstate->digital_addresses = $realEstate->digital_addresses . "-" . $realEstate->id;
        $realEstate->categories()->sync($request->categories);

        $realEstate->save();

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload branch images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('place', $image);
                $path = $this->resizeImage($image, 'realstate', 800);
                $realEstate->images()->create(['image' => $path]);
            }
        }

        return redirect()->route('admin.real-estate.index')->with($this->setMessage('Real Estate Update successfully', self::MESSAGE_SUCCESS));
    }

    protected function generateDigitalAddress($request)
    {
        return substr($request->province, 0, 1) . 'P' . str_pad($request->ward, 3, "0", STR_PAD_LEFT) . ' ' . $request->latitude . ' ' . $request->longitude;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\RealEstate $realEstate
     * @return \Illuminate\Http\Response
     */
    public function destroy(RealEstate $realEstate)
    {
        $realEstate->delete();

        return redirect()->route('admin.real-estate.index')
            ->with($this->setMessage('Real Estate Deleted Successfully', self::MESSAGE_SUCCESS));
    }

    public function imageDelete($id)
    {

        $realEstateImage = RealEstateImage::findOrFail($id);

        if ($realEstateImage->image) {
            \Storage::disk('public')->delete($realEstateImage->image);
        }
        $realEstateImage->delete();

        return response()->json(['status' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Auto $auto
     * @return \Illuminate\Http\Response
     */
    public function interestedUsers(RealEstate $realEstate)
    {

        //$interestedUser = $auto->load('users');
        //dd($interestedUser->users);
        $query = User::whereHas('interestedRealEstate', function ($q) use ($realEstate) {
            $q->where('real_estate_id', '=', $realEstate->id);
        });

        $grid = (new Grid())
            ->setQuery($query)
            ->setColumns([
                [
                    'field' => 'name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'email',
                    'label' => 'Email',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'mobile_number',
                    'label' => 'Mobile',
                    'sortable' => true,
                    'filterable' => true
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
                    'icon' => 'remove_red_eye',
                    'url' => function ($item) {
                        return route('admin.user.show', $item->id);
                    }
                ],

            ])->generate();

        return view('admin.realestate.interested', compact('grid'));
    }
}
