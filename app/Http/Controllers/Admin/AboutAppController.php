<?php

namespace App\Http\Controllers\Admin;
use App\Models\AboutApp;
use Illuminate\Http\Request;

class AboutAppController extends AdminController
{
    //
    public function index()
    {
        $aboutApp = AboutApp::first();
        return view('admin.aboutapp.view', compact('aboutApp'));
    }

    public function update(Request $request)
    {
        $aboutApp = AboutApp::first();

        if (!$aboutApp) {
            AboutApp::create([
                'aboutAppInfo' => $request->aboutAppInfo
            ]);
        } else {
            $aboutApp->update([
                'aboutAppInfo' => $request->aboutAppInfo
            ]);
        }

        return redirect()->route('admin.aboutapp.index')->with('success', 'About App updated successfully!');
    }
}
