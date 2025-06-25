<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IntellectualProperty;

class IntellectualPropertyController extends AdminController
{
    //
    public function index()
    {
        $intellectualProperty = IntellectualProperty::first();
        return view('admin.intellectualproperty.view' ,  compact('intellectualProperty')); //need to change according to privacy policy
    }

    public function update(Request $request)
    {
        $intellectualProperty = IntellectualProperty::first();

        if (!$intellectualProperty) {
            IntellectualProperty::create([
                'title' => $request->title
            ]);
        } else {
            $intellectualProperty->update([
                'title' => $request->title
            ]);
        }

        return redirect()->route('admin.intellectual.index')->with('success', 'Legal Terms updated successfully!');//need to change according to privacy policy
    }
}
