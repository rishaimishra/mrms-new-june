<?php

namespace App\Http\Controllers\Admin\Place;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\Place;
use App\Models\PlaceCategory;
use App\Models\PlaceImage;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
{
    public function index()
    {
        $query = Place::with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')->latest();
        $results = $query->paginate();
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
                    'formatter' => function ($field, Place $Place) {
                        return $Place->categories()->pluck('name')->implode(', ');
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
                        return route('admin.place.edit', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.place.grid', compact('grid'));
    }

    public function create()
    {
        $categories = PlaceCategory::get();
        $place = new Place();
        return view('admin.place.edit', compact('place', 'categories'));
    }

    protected function validator($request, $isUpdate = false)
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
            'images' => ($isUpdate ? 'nullable' : 'required') . '|array',
            'images.*' => 'required|file|mimes:jpg,jpeg,png'
        ]);
    }

    public function store(Request $request)
    {
        $this->validator($request)->validate();

        $digital_addresses = $this->generateDigitalAddress($request); //EP002 24.585188 73.709946

        $place = new Place();

        $place->digital_addresses = $digital_addresses ?? 0;
        $place->title = $request->input('title');
        $place->name = $request->input('name');
        $place->about = $request->input('about', '');
        $place->type = Place::TYPE_PLACE;
        $place->map_addresses = $request->input('address', '');
        $place->latitude = $request->input('latitude');
        $place->longitude = $request->input('longitude');
        $place->availability_times = json_decode($request->input('availability_times', ''));
        $place->meta_tag1 = $request->input('meta_tag1', '');
        $place->meta_tag2 = $request->input('meta_tag2', '');
        $place->meta_tag3 = $request->input('meta_tag3', '');
        $place->sequence = $request->input('sequence', '');
        $place->address()->associate($request->input('address_id'));
        $place->addressArea()->associate($request->input('address_area_id'));
        $place->addressChiefdom()->associate($request->input('address_chiefdom_id'));
        $place->addressSection()->associate($request->input('address_section_id'));

        $request->user()->places()->save($place);

        $place->categories()->sync($request->categories);

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 10) {
                return redirect()->back()->with($this->setMessage('You can not upload branch images more then 10', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('place', $image);
                $path = $this->resizeImage($image, 'place', 800);
                $place->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.place.index')->with($this->setMessage('Place Saved Successfully', self::MESSAGE_SUCCESS));
    }

    public function edit(Place $place, Request $request)
    {
        $categories = PlaceCategory::get();
        //$place = Place::with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')->findOrFail($place->id)->first();
        $place->load('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection');
        return view('admin.place.edit', compact('categories', 'place'));
    }

    public function update(Place $place, Request $request)
    {

        $this->validator($request, true)->validate();
        $digitalAddresses = $this->generateDigitalAddress($request); //EP002 24.585188 73.709946

        $place->digital_addresses = $digitalAddresses ?? 0;
        $place->title = $request->input('title');
        $place->name = $request->name;
        $place->about = $request->input('about', '');
        $place->type = Place::TYPE_PLACE;
        $place->map_addresses = $request->address ?? '';
        $place->latitude = $request->latitude;
        $place->longitude = $request->longitude;
        $place->availability_times = json_decode(stripslashes($request->input('availability_times', '')));
        $place->meta_tag1 = $request->input('meta_tag1', '');
        $place->meta_tag2 = $request->input('meta_tag2', '');
        $place->meta_tag3 = $request->input('meta_tag3', '');
        $place->sequence = $request->input('sequence', '');
        $place->address()->associate($request->address_id);
        $place->addressArea()->associate($request->address_area_id);
        $place->addressChiefdom()->associate($request->address_chiefdom_id);
        $place->addressSection()->associate($request->address_section_id);
        $place->save();

        $place->digital_addresses = $place->digital_addresses . "-" . $place->id;
        $place->categories()->sync($request->categories);
        $place->save();

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 10) {
                return redirect()->back()->with($this->setMessage('You can not upload branch images more then 10', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('place', $image);
                $path = $this->resizeImage($image, 'place', 800);
                $place->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.place.index')->with($this->setMessage('Place Update Successfully', self::MESSAGE_SUCCESS));
    }

    protected function generateDigitalAddress($request)
    {
        return substr($request->province, 0, 1) . 'P' . str_pad($request->ward, 3, "0", STR_PAD_LEFT) . ' ' . $request->latitude . ' ' . $request->longitude;
    }

    public function destroy(Place $place, Request $request)
    {

        $place->delete();
        return redirect()->route('admin.place.index')
            ->with($this->setMessage('Place Deleted Successfully', self::MESSAGE_SUCCESS));
    }

    public function imageDelete($id)
    {


        $placeImage = PlaceImage::findOrFail($id);

        if ($placeImage->image) {
            \Storage::disk('public')->delete($placeImage->image);
        }
        $placeImage->delete();

        return response()->json(['status' => true]);
    }
}
