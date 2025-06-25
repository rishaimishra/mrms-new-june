<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\OnlineBookingCategory;

class OnlineBookCategoryController extends Controller
{
    public function index()
    {
        $categories = OnlineBookingCategory::all();
        return view('admin.online.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.online.create');
    }

    public function store(Request $request)
    {
        // return $request;
        // dd($request);
        $request->validate([
            'name' => 'required|string|max:191',
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        $serviceCategory = OnlineBookingCategory::create($request->except(['online_image', 'background_image']));
        
        // Store 'image_chat' if it is uploaded
        if ($request->hasFile('online_image')) {
            $filePath = $request->file('online_image')->store('product_category', 'public');
            $serviceCategory->image = $filePath;
        }
        
        // Store 'background_image' if it is uploaded
        if ($request->hasFile('background_image')) {
            $filePath = $request->file('background_image')->store('product_category', 'public');
            $serviceCategory->background_image = $filePath;
        }
        
        // Save the updated paths
        $serviceCategory->save();
        return redirect()->route('admin.online')->with('success', 'Service Category created successfully.');
    }

    public function edit($id)
    {
        $serviceCategory = OnlineBookingCategory::where('id','=',$id)->first();
        return view('admin.online.edit', compact('serviceCategory'));
    }

    public function update(Request $request)
    {
        // return $request;
        // dd($request);
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:191',
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

    // Find the serviceCategory by id
    $serviceCategory = OnlineBookingCategory::findOrFail($request->serviceId);

    if ($request->hasFile('online_image')) {
        // Delete the old image if it exists
       

        // Store the new image in the correct directory and update the file path in the database
        $filePath = $request->file('online_image')->store('product_category', 'public');
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

        return redirect()->route('admin.online')->with('success', 'Online Category updated successfully.');
    }
}
