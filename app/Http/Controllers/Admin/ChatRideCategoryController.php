<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ChatArideCategory;
use Illuminate\Support\Facades\Storage;


class ChatRideCategoryController extends Controller
{
    public function index()
    {
        $categories = ChatArideCategory::all();
        return view('admin.chatride.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.chatride.create');
    }

    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required|string|max:191',
            'image_chat' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'background_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);
        
        // Create the category instance
        $serviceCategory = ChatArideCategory::create($request->except(['image_chat', 'background_image']));
        
        // Store 'image_chat' if it is uploaded
        if ($request->hasFile('image_chat')) {
            $filePath = $request->file('image_chat')->store('product_category', 'public');
            $serviceCategory->image = $filePath;
        }
        
        // Store 'background_image' if it is uploaded
        if ($request->hasFile('background_image')) {
            $filePath = $request->file('background_image')->store('product_category', 'public');
            $serviceCategory->background_image = $filePath;
        }
        
        // Save the updated paths
        $serviceCategory->save();
        return redirect()->route('admin.chat_a_ride.list')->with('success', 'Chat-a-ride Category created successfully.');
    }

    public function edit($id)
    {
        $serviceCategory = ChatArideCategory::where('id','=',$id)->first();
        return view('admin.chatride.edit', compact('serviceCategory'));
    }

    public function update(Request $request)
    {
        // return $request;
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:191',
            'image_chat' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust size/format for images
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust size/format for images
            'is_active' => 'required|boolean',
            'sequence' => 'nullable|integer',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

    // Find the serviceCategory by id
     $serviceCategory = ChatArideCategory::findOrFail($request->serviceId);

     // Handle image upload if a new image is provided
     if ($request->hasFile('image_chat')) {
        // Delete the old image if it exists
        

        // Store the new image in the correct directory and update the file path in the database
        $filePath = $request->file('image_chat')->store('product_category', 'public');
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
        'background_image' => $serviceCategory->background_image
    ]);

        return redirect()->route('admin.chat_a_ride.list')->with('success', 'Chata ride Category updated successfully.');
    }

    public function destroy(ChatArideCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('admin.chat_a_ride.list')->with('success', 'Chat A Ride Category deleted successfully.');
    }
}
