<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyValue;

class PrivacyPolicyController extends AdminController
{
    //
    public function index()
    {
        $privacyValue = PrivacyValue::first();
        return view('admin.privacypolicy.view' ,  compact('privacyValue')); //need to change according to privacy policy
    }

    public function update(Request $request)
    {
        $privacyValue = PrivacyValue::first();

        if (!$privacyValue) {
            PrivacyValue::create([
                'title' => $request->title
            ]);
        } else {
            $privacyValue->update([
                'title' => $request->title
            ]);
        }

        return redirect()->route('admin.privacy.index')->with('success', 'Legal Terms updated successfully!');//need to change according to privacy policy
    }
}
