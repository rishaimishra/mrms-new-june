<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\SellerTheme;

class SellerThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $sellerThemes = SellerTheme::all();
        return view('sellerThemes.create', compact('sellerThemes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'theme_name' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Adjust as needed
        ]);

        // Handle file upload
        if ($request->hasFile('theme_name')) {
            $fileName = time() . '_' . $request->file('theme_name')->getClientOriginalName();
            $path = $request->file('theme_name')->storeAs('theme_uploads', $fileName, 'public');

            // Store in the database
            SellerTheme::create([
                'theme_name' => $path,  // Save the file path
            ]);
        }

        return redirect()->route('admin.seller-themes.create')->with('success', 'Theme uploaded successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        $theme = SellerTheme::find($id);
        

        if (!$theme) {
            return redirect()->route('admin.seller-themes.create')->with('error', 'Theme not found.');
        }

        // Delete the theme
        $theme->delete();

        return redirect()->route('admin.seller-themes.create')->with('success', 'Theme deleted successfully.');
    }
}
