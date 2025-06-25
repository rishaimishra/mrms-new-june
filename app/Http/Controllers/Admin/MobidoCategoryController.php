<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MobiDoCategory;
use Illuminate\Support\Facades\Storage;

class MobidoCategoryController extends Controller
{
    public function index()
    {
        $categories = MobiDoCategory::all();
        return view('admin.mobidoc.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.mobidoc.create');
    }

    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required|string|max:191',
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

                    
        $serviceCategory = MobiDoCategory::create($request->except(['mobi_image', 'background_image']));
        
        // Store 'image_chat' if it is uploaded
        if ($request->hasFile('mobi_image')) {
            $filePath = $request->file('mobi_image')->store('product_category', 'public');
            $serviceCategory->image = $filePath;
        }
        
        // Store 'background_image' if it is uploaded
        if ($request->hasFile('background_image')) {
            $filePath = $request->file('background_image')->store('product_category', 'public');
            $serviceCategory->background_image = $filePath;
        }
        
        // Save the updated paths
        $serviceCategory->save();
        return redirect()->route('admin.movie_doc')->with('success', 'Service Category created successfully.');
    }

    public function edit($id)
    {
        $serviceCategory = MobiDoCategory::where('id','=',$id)->first();
        return view('admin.mobidoc.edit', compact('serviceCategory'));
    }

    public function update(Request $request)
    {
        // dd($request);
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:191',
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

    // Find the serviceCategory by id
    $serviceCategory = MobiDoCategory::findOrFail($request->serviceId);

     // Handle image upload if a new image is provided
     if ($request->hasFile('mobi_image')) {
        // Delete the old image if it exists
       

        // Store the new image in the correct directory and update the file path in the database
        $filePath = $request->file('mobi_image')->store('product_category', 'public');
        $serviceCategory->image = $filePath; // Update the image path in the model
    }
     if ($request->hasFile('background_image')) {
        // Delete the old image if it exists
       

        // Store the new image in the correct directory and update the file path in the database
        $filePath = $request->file('background_image')->store('product_category', 'public');
        $serviceCategory->background_image = $filePath; // Update the image path in the model
    }

    // Update the serviceCategory with the other request data
    $serviceCategory->update([
        'name' => $request->name,
        'is_active' => $request->is_active,
        'sequence' => $request->sequence,
        'sponsor_text' => $request->sponsor_text,
        'image' => $serviceCategory->image,
        'background_image' =>  $serviceCategory->background_image
    ]);

        return redirect()->route('admin.movie_doc')->with('success', 'Chata ride Category updated successfully.');
    }


    public function destroy(MobiDoCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('admin.movie_doc')->with('success', 'Service Category deleted successfully.');
    }
}
