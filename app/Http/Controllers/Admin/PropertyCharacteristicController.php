<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\PropertyCharacteristicGrid;
use App\Models\AdminUser;
use App\Models\PropertyCharacteristic;
use App\Models\Property;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PropertyCharacteristicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PropertyCharacteristic::query();
        
        //dd(District::where('id',13)->get());
        return (new PropertyCharacteristicGrid())
            ->create(['query' => $query, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.propertycharacteristic.index');
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

        $propertycharacteristic = PropertyCharacteristic::find($id);

        return view('admin.propertycharacteristic.edit', compact('propertycharacteristic'));
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
            'name' => ['required', 'max:70'],
        ]);
        $propertycharacteristic = PropertyCharacteristic::findOrFail($id);
        $propertycharacteristic->name = $request->name;


        $propertycharacteristic->save();

        return redirect()->back()->with('success', 'Property Characteristic successfully updated');
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
            $district = PropertyCharacteristic::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.propertycharacteristic.index')->with('success', 'Property Characteristic Deleted Successfully !');
        } else {
            return Redirect()->route('admin.propertycharacteristic.index')->with('error', "You can't delete Property Characteristic. ");
        }
    }
}
