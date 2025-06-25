<?php

namespace App\Http\Controllers\Admin\Auto;


use App\Http\Controllers\Admin\AdminController;
use App\Library\Grid\Grid;
use App\Models\Auto;
use App\Models\AutoCategory;
use App\Models\AutoImage;
use App\Models\User;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Eav\Entity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AutoController extends AdminController
{
    const ENTITY_ID = 1;

    public function index()
    {
        $query = Auto::with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')
            ->latest();

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
                    'formatter' => function ($field, Auto $Auto) {
                        return $Auto->categories()->pluck('name')->implode(', ');
                    }
                ],
                [
                    'field' => 'is_available',
                    'label' => 'Is Available ',
                    'sortable' => true,
                    'formatter' => function ($field, Auto $Auto) {
                        return $Auto->is_available ? "Yes" : "No";
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
                        return route('admin.auto.edit', $item->id);
                    }
                ],
                [
                    'label' => 'Interested user',
                    'icon' => 'verified_user',
                    'url' => function ($item) {
                        return route('admin.auto.interested.users', $item->id);
                    }
                ]

            ])->generate();

        return view('admin.auto.grid', compact('grid'));
    }

    public function create()
    {
        $categories = AutoCategory::get();
        $auto = new Auto();

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        return view('admin.auto.edit', compact('auto', 'categories', 'attributeSets'));
    }

    protected function validator($request, $additionalRules = [], $isUpdate = false)
    {
        return validator($request->all(), [
                'name' => 'required|string|max:191',
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
                'images' => ($isUpdate ? 'nullable' : 'required') . '|array|max:20',
                'images.*' => 'required|file|mimes:jpg,jpeg,png'
            ] + $additionalRules);
    }

    public function store(Request $request)
    {
        // return $request;
        $this->validator($request, [
            'attribute_set_id' => ['required']
        ])->validate();

        $digital_addresses = $this->generateDigitalAddress($request); //EP002 24.585188 73.709946

        $auto = new Auto();

        $auto->entity_id = self::ENTITY_ID;
        $auto->user_id = Auth::user()->id;
        $auto->digital_addresses = $digital_addresses ?? 0;
        $auto->title = $request->input('title');
        $auto->name = $request->input('name');
        $auto->about = $request->input('about', '');
        $auto->type = Auto::TYPE_PLACE;
        $auto->map_addresses = $request->input('address', '');
        $auto->latitude = $request->input('latitude');
        $auto->longitude = $request->input('longitude');
        $auto->availability_times = json_decode($request->input('availability_times', ''));
        $auto->meta_tag1 = $request->input('meta_tag1', '');
        $auto->meta_tag2 = $request->input('meta_tag2', '');
        $auto->meta_tag3 = $request->input('meta_tag3', '');
        $auto->sequence = $request->input('sequence', '');
        $auto->is_available = $request->is_available;
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            // Get the original filename
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Save the image in the 'public/images' folder
            $file->move(public_path('product_background_images'), $filename);

            // You can also store the path to the database if needed
            // Image::create(['path' => 'images/' . $filename]);
            $auto->background_image = $filename;
        }
        $auto->fill([
            'entity_id' => self::ENTITY_ID,
            'attribute_set_id' => $request->attribute_set_id
        ]);

        $auto->forceFill([
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $auto->address()->associate($request->input('address_id'));
        $auto->addressArea()->associate($request->input('address_area_id'));
        $auto->addressChiefdom()->associate($request->input('address_chiefdom_id'));
        $auto->addressSection()->associate($request->input('address_section_id'));
        
         $auto->save();
        // return $auto;
        // $request->user()->places()->save($auto);

        $auto->categories()->sync($request->categories);
        
        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload branch images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('place', $image);
                $path = $this->resizeImage($image, 'auto', 800);
                $auto->images()->create(['image' => $path]);
            }
        }

        return redirect()->route('admin.auto.index')->with($this->setMessage('Auto Saved Successfully', self::MESSAGE_SUCCESS));
    }

    public function show(Auto $auto)
    {
        //
    }

    public function edit(Auto $auto)
    {
        $auto = Auto::select(['*', 'attr.*'])->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')
            ->find($auto->id);

        $categories = AutoCategory::get();

        $attributeGroups = $this->getAttributes($auto);

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        return view('admin.auto.edit', compact('categories', 'auto', 'attributeGroups', 'attributeSets'));
    }

    public function update(Request $request, Auto $auto)
    {
        if ($request->input('attribute_set_id')) {
            $auto->attribute_set_id = $request->input('attribute_set_id');
            $auto->save();
        }

        $attributeGroups = $this->getAttributes($auto);

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

        $auto->digital_addresses = $digitalAddresses ?? 0;
        $auto->title = $request->input('title');
        $auto->name = $request->name;
        $auto->about = $request->input('about', '');
        $auto->type = Auto::TYPE_PLACE;
        $auto->map_addresses = $request->address ?? '';
        $auto->latitude = $request->latitude;
        $auto->longitude = $request->longitude;
        $auto->availability_times = json_decode(stripslashes($request->input('availability_times', '')));
        $auto->meta_tag1 = $request->input('meta_tag1', '');
        $auto->meta_tag2 = $request->input('meta_tag2', '');
        $auto->meta_tag3 = $request->input('meta_tag3', '');
        $auto->sequence = $request->input('sequence', '');
        $auto->is_available = $request->is_available;
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            // Get the original filename
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Save the image in the 'public/images' folder
            $file->move(public_path('product_background_images'), $filename);

            // You can also store the path to the database if needed
            // Image::create(['path' => 'images/' . $filename]);
            $auto->background_image = $filename;
        }
        $auto->address()->associate($request->address_id);
        $auto->addressArea()->associate($request->address_area_id);
        $auto->addressChiefdom()->associate($request->address_chiefdom_id);
        $auto->addressSection()->associate($request->address_section_id);

        $auto->save();

        $auto->digital_addresses = $auto->digital_addresses . "-" . $auto->id;
        $auto->categories()->sync($request->categories);

        foreach ($attributeValues as $key => $attr) {

            $field = array_keys($attr)[0];
            $val = $attr[$field];

            $auto->{$field} = $val;
        }

        $auto->save();

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload branch images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('place', $image);
                $path = $this->resizeImage($image, 'auto', 800);
                $auto->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.auto.index')->with($this->setMessage('Auto Update Successfully', self::MESSAGE_SUCCESS));
    }

    public function destroy(Auto $auto)
    {
        $auto->delete();

        return redirect()->route('admin.auto.index')
            ->with($this->setMessage('Auto Deleted Successfully', self::MESSAGE_SUCCESS));
    }

    protected function getAttributes(Auto $auto)
    {
        $attributeSet = AttributeSet::find($auto->attribute_set_id);

        if (! $attributeSet) {
            return false;
        }

        return $attributeSet->groups()->whereHas('attributes')->with('attributes.optionValues')->get();
    }

    protected function generateDigitalAddress($request): string
    {
        return substr($request->province, 0, 1) . 'P' . str_pad($request->ward, 3, "0", STR_PAD_LEFT) . ' ' . $request->latitude . ' ' . $request->longitude;
    }

    public function imageDelete($id): JsonResponse
    {
        //AutoImage::where('id',$id)->delete();
        $autoImage = AutoImage::findOrFail($id);

        if ($autoImage->image) {
            Storage::disk('public')->delete($autoImage->image);
        }

        $autoImage->delete();

        return response()->json(['status' => true]);
    }

    public function imagebgDelete($id): JsonResponse
    {
        //AutoImage::where('id',$id)->delete();
        $autobgImage = Auto::findOrFail($id);

        $autobgImage->background_image = null;

        $autobgImage->save();

        return response()->json(['status' => true]);
    }

    public function interestedUsers(Auto $auto)
    {

        //$interestedUser = $auto->load('users');
        //dd($interestedUser->users);
        $query = User::whereHas('interestedAutos', function ($q) use ($auto) {
            $q->where('auto_id', '=', $auto->id);
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

        return view('admin.auto.interested', compact('grid'));
    }
}
