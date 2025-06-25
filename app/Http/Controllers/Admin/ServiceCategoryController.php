<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ServiceCategory;

class ServiceCategoryController extends Controller
{
   
    public function index()
    {
        // Load categories with parent relationships
        $categories = ServiceCategory::with('parent')->get();

        return view('admin.services.services', compact('categories'));
    }


    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        $serviceCategory = ServiceCategory::create($request->except(['service_image', 'background_image']));
        
        // Store 'image_chat' if it is uploaded
        if ($request->hasFile('service_image')) {
            $filePath = $request->file('service_image')->store('product_category', 'public');
            $serviceCategory->image = $filePath;
        }
        
        // Store 'background_image' if it is uploaded
        if ($request->hasFile('background_image')) {
            $filePath = $request->file('background_image')->store('product_category', 'public');
            $serviceCategory->background_image = $filePath;
        }
        
        // Save the updated paths
        $serviceCategory->save();
        return redirect()->route('admin.services.list')->with('success', 'Service Category created successfully.');
    }

    public function edit($id)
    {
        $serviceCategory = ServiceCategory::findOrFail($id);
        $parentCategories = ServiceCategory::where('id', '!=', $id)->get(); // Exclude the current category
        return view('admin.services.edit', compact('serviceCategory', 'parentCategories'));
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
            'parent_id' => 'nullable', // Validate that parent_id exists in the service_categories table
        ]);

        // Find the serviceCategory by id
        $serviceCategory = ServiceCategory::findOrFail($request->serviceId);

        
        if ($request->hasFile('service_image')) {
            // Delete the old image if it exists
        

            // Store the new image in the correct directory and update the file path in the database
            $filePath = $request->file('service_image')->store('product_category', 'public');
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
            'background_image' =>  $serviceCategory->background_image,
            'parent_id' => $request->parent_id, // Add parent_id to the update
        ]);

        return redirect()->route('admin.services.list')->with('success', 'Service Category updated successfully.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('admin.services.list')->with('success', 'Service Category deleted successfully.');
    }
}
