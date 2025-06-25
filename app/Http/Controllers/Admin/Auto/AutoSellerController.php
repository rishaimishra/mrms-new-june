<?php

namespace App\Http\Controllers\Admin\Auto;


use App\Http\Controllers\Admin\AdminController;
use App\Library\Grid\Grid;
use App\Models\Auto;
use App\Models\RealEstate;
use App\Models\RealEstateCategory;
use App\Models\AutoCategory;
use App\Models\AutoImage;
use App\Models\User;
use App\Models\AddressArea;
use App\Models\AddressSection;
use App\Models\AddressChiefdom;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Eav\Entity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AutoSellerController extends AdminController
{
    const ENTITY_ID = 1;

    public function auto_edit($id = null)
    {
        // If we are editing an existing auto, retrieve it
        $auto = $id ? Auto::find($id) : null;
    
        // Retrieve all attribute sets for the dropdown
        $attributeSets = AttributeSet::all();
    
        // If the auto exists, load its current attribute set
        $autoAttributeSetId = $auto ? $auto->attribute_set_id : null;
    
        // Retrieve attribute groups based on the selected attribute set
        $attributeGroups = $autoAttributeSetId ? AttributeGroup::where('attribute_set_id', $autoAttributeSetId)->get() : [];
    
         // If the auto exists, we fetch the associated attribute group ID(s)
    $autoAttributeGroupIds =$attributeGroups->pluck('attribute_group_id')->toArray(); // Assuming the relationship exists

  

        $attributes = 
            Attribute::where('entity_id', 1)->get();
    
        // Retrieve all auto categories for the category selection
        $autoCategories = AutoCategory::all();
    
        // If the auto exists, load its current categories
        $autoCategoryIds = $auto ? $auto->categories->pluck('id')->toArray() : [];

    
        // Pass the data to the view
        return view('admin.productseller.autoedit', compact('auto', 'attributeSets', 'attributeGroups', 'attributes', 'autoCategories', 'autoCategoryIds', 'autoAttributeSetId', 'autoAttributeGroupIds','auto'));
    }

   

    public function auto_edit_save_similar(Request $request, $id = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'about' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate images
        ]);

        // Fetch the existing auto record by $id
        $auto = Auto::findOrFail($id); // This will throw a 404 error if not found
      
        // Copy the attributes for the new record
      $newAuto = $auto->replicate(); // This will create a copy without the ID
        // Ensure that the user_id is copied over
        $user= Auth::user()->id;
    $newAuto->user_id = $user;  // Make sure the user_id is set correctly

    // Optionally, if you want to modify some fields before saving (e.g., setting a different name or title), you can do so here
    $newAuto->name = $request->input('name', $auto->name);
    $newAuto->title = $request->input('title', $auto->title);
    $newAuto->about = $request->input('about', $auto->about);
    
    // Save the new record in the database
    $newAuto->save();

    $category_ids = $auto->categories->pluck('id')->toArray(); // Get all associated category IDs
    $newAuto->categories()->attach($category_ids); // Attach the same categories to the new Auto


       
        // Handle Image Uploads
        if ($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                // Store the image in the public storage
                $path = $image->store('autos', 'public');

                // Save the image details in the auto_images table
                AutoImage::create([
                    'auto_id' => $auto->id,
                    'image' => $path
                ]);
            }
        }

        return redirect()->back()->with('success', 'Auto saved successfully!');
    }

    public function AutoMyindex(){
        $MyAutos = Auto::all();
        return view('admin.productseller.myautolist', compact('MyAutos'));
    }
    
    public function MyAutoedit($id){
        $auto = Auto::findOrFail($id);
        $auto = Auto::select(['*', 'attr.*'])->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')
        ->find($auto->id);

    $categories = AutoCategory::get();

    $attributeGroups = $this->getAttributes($auto);

    $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');
    return view('admin.productseller.myautoedit', compact('categories', 'auto', 'attributeGroups', 'attributeSets'));
    }

    public function MyAutoeditsave(Request $request, Auto $auto)
    {
        $auto = Auto::findOrFail($request->auto_id);

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

        // $this->validator($request, $additionalRules, true)->validate();
        
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
        return redirect()->route('admin.edit-seller-product')->with($this->setMessage('Auto Update Successfully', self::MESSAGE_SUCCESS));
    }

    protected function getAttributes(Auto $auto)
    {
        $attributeSet = AttributeSet::find($auto->attribute_set_id);

        if (! $attributeSet) {
            return false;
        }

        return $attributeSet->groups()->whereHas('attributes')->with('attributes.optionValues')->get();
    }
    protected function getAttributesReal(RealEstate $auto)
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

    public function edit_property($id){

        $auto = RealEstate::findOrFail($id);
        $auto = RealEstate::select(['*', 'attr.*'])->with('address', 'address.addressArea', 'address.addressChiefdom', 'address.addressSection', 'addressArea', 'addressChiefdom', 'addressSection')
        ->find($auto->id);

    $categories = RealEstateCategory::get();

    $attributeGroups = $this->getAttributesReal($auto);

    $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');
    return view('admin.productseller.myrealedit', compact('categories', 'auto', 'attributeGroups', 'attributeSets'));
   
    }

}
