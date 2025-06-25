<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cookies;

class CookiesController extends AdminController
{
    //
    public function index()
    {
        $cookies = Cookies::first();
        return view('admin.cookies.view' ,  compact('cookies')); //need to change according to privacy policy
    }

    public function update(Request $request)
    {
        $cookies = Cookies::first();

        if (!$cookies) {
            Cookies::create([
                'title' => $request->title
            ]);
        } else {
            $cookies->update([
                'title' => $request->title
            ]);
        }

        return redirect()->route('admin.cookies.index')->with('success', 'Legal Terms updated successfully!');//need to change according to privacy policy
    }
}
