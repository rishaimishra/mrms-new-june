<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\MillRateGrid;
use App\Models\AdminUser;
use App\Models\MillRate;
use App\Models\Property;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class MillRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = MillRate::query();
        
        //dd(District::where('id',13)->get());
        return (new MillRateGrid())
            ->create(['query' => $query, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.millrate.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $millrate = MillRate::find($id);

        return view('admin.millrate.edit', compact('millrate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $request->validate( [
            'rate' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);
        $millrate = MillRate::findOrFail($id);
        $millrate->rate = $request->rate;


        $millrate->save();

        return redirect()->back()->with('success', 'Mill rate successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (request()->user()->hasRole('Super Admin')) {
            $district = MillRate::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.millrates.index')->with('success', 'Mill rate Deleted Successfully !');
        } else {
            return Redirect()->route('admin.millrates.index')->with('error', "You can't delete Mill rate. ");
        }
    }
}
