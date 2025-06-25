<?php


namespace App\Http\Controllers\Admin\Product;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
class SellerrProductController extends Controller
{
    const ENTITY_ID = 3;

    public function index()
    {

        $grid = (new Grid())
            ->setQuery(Product::latest())
            ->setColumns([
                [
                    'field' => 'name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'quantity',
                    'label' => 'Quantity',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'weight',
                    'label' => 'Weight (in KG)',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'price',
                    'label' => 'Price (in Leones)',
                    'sortable' => true,
                    'filterable' => true,
                    'formatter' => function ($field, Product $product) {
                        return is_numeric($product->price) ? number_format($product->price, 2) : $product->price;
                    }
                ],
                [
                    'field' => 'unit',
                    'label' => 'Unit',
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
                        return route('admin.product.edit', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.product.grid', compact('grid'));
    }

    public function create()
    {
        $categories = ProductCategory::get();
        $product = new Product();

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        return view('admin.product.edit', compact('categories', 'product', 'attributeSets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'attribute_set_id' => ['required'],
            'quantity' => 'required|numeric|min:1|max:999999',
            'weight' => 'required|numeric|min:0|max:999999',
            'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
            'unit' => 'required|string|max:50',
            'images' => 'required|array',
            'stock_availability' => ['required', Rule::in(Product::STOCK_AVAILABILITY_OPTIONS)],
            'images.*' => 'required|file|mimes:jpg,jpeg,png',
            'categories' => 'required|array',
            'categories.*' => 'required||exists:product_categories,id',
            'sequence' => 'nullable|numeric|min:1|max:999999',
        ]);

        $product = new Product();
        $product->entity_id = self::ENTITY_ID;
        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->price = str_replace(',', '', $request->price);
        $product->unit = $request->unit;
        $product->sequence = $request->input('sequence', '');
        $product->attribute_set_id = $request->attribute_set_id;
        $product->stock_availability = $request->input('stock_availability');
        $product->user_id = Auth::user()->id;
        $product->forceFill([
            'created_at' => now(),
            'updated_at' => now()
        ]);
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            // Get the original filename
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Save the image in the 'public/images' folder
            $file->move(public_path('product_background_images'), $filename);

            // You can also store the path to the database if needed
            // Image::create(['path' => 'images/' . $filename]);
            $product->background_image = $filename;
            $product->save();
        }
        $product->save();

        $product->categories()->sync($request->categories);

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload product images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('basket', $image);
                $path = $this->resizeImage($image, 'basket', 800);
                $product->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.product.index')->with($this->setMessage('Product Saved Successfully', self::MESSAGE_SUCCESS));
    }

    protected function getAttributes(Product $product)
    {
        $attributeSet = AttributeSet::find($product->attribute_set_id);

        if (! $attributeSet) {
            return false;
        }

        return $attributeSet->groups()->whereHas('attributes')->with('attributes.optionValues')->get();
    }

    public function edit($id, Request $request)
    {
        $product = Product::select(['*', 'attr.*'])->findOrFail($id);

        $categories = ProductCategory::get();

        $attributeGroups = $this->getAttributes($product);

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        return view('admin.product.edit', compact('categories', 'product', 'attributeGroups', 'attributeSets'));
    }

    public function update(Product $product, Request $request)
    {
        if ($request->input('attribute_set_id')) {
            $product->attribute_set_id = $request->input('attribute_set_id');
            $product->save();
        }

        $attributeGroups = $this->getAttributes($product);

        $additionalRules = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
            return $attributeGroup->attributes->map(function (Attribute $attribute) {
                return [
                    $attribute->attribute_code => [$attribute->is_required ? 'required' : 'nullable', 'string']
                ];
            });
        })->toArray();

        $request->validate([
            'name' => 'required|string|max:191',
            'quantity' => 'required|numeric|min:1|max:999999',
            'weight' => 'required|numeric|min:0|max:999999',
            'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
            'unit' => 'required|string|max:50',
            'images' => 'nullable|array',
            'stock_availability' => ['required', Rule::in(Product::STOCK_AVAILABILITY_OPTIONS)],
            'images.*' => 'required|file|mimes:jpg,jpeg,png',
            'categories' => 'required|array',
            'categories.*' => 'required||exists:product_categories,id',
            'sequence' => 'nullable|numeric|min:1|max:9999',
        ] + $additionalRules);

        $attributeValues = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
            return $attributeGroup->attributes->pluck('attribute_code')->map(function ($code) {
                return [$code => \request($code)];
            });
        })->toArray();

        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->price = str_replace(',', '', $request->price); //$request->price;
        $product->unit = $request->unit;
        $product->sequence = $request->input('sequence', '');
        $product->stock_availability = $request->input('stock_availability');

        foreach ($attributeValues as $key => $attr) {

            $field = array_keys($attr)[0];
            $val = $attr[$field];

            $product->{$field} = $val;
        }
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            // Get the original filename
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Save the image in the 'public/images' folder
            $file->move(public_path('product_background_images'), $filename);

            // You can also store the path to the database if needed
            // Image::create(['path' => 'images/' . $filename]);
            $product->background_image = $filename;
            $product->save();
        }
        $product->save();
        $product->categories()->sync($request->categories);

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload product images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('basket', $image);
                $path = $this->resizeImage($image, 'basket', 800);
                $product->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.product.index')->with($this->setMessage('Product Update Successfully', self::MESSAGE_SUCCESS));
    }

    public function destroy(Product $product, Request $request)
    {
        $product->cart()->delete();
        $product->delete();

        return redirect()->route('admin.product.index')
            ->with($this->setMessage('Product Deleted Successfully', self::MESSAGE_SUCCESS));
    }

    public function imageDelete($id)
    {

        $productImage = ProductImage::findOrFail($id);

        if ($productImage->image) {
            \Storage::disk('public')->delete($productImage->image);
        }

        $productImage->delete();

        return response()->json(['status' => true]);
    }
    public function bgimageDelete($id)
    {
        // return $id;
        $productImage = Product::findOrFail($id);
        $productImage->background_image = null;
        $productImage->save();
        return response()->json(['status' => true]);
    }
}