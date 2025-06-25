<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\PropertyCharacteristicValuesGrid;
use App\Models\AdminUser;
use App\Models\PropertyCharacteristicValue;
use App\Models\Property;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PropertyCharacteristicValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PropertyCharacteristicValue::query();
        
        //dd(District::where('id',13)->get());
        return (new PropertyCharacteristicValuesGrid())
            ->create(['query' => $query, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.adjustmentvalue.index');
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

        $propertycharacteristicvalue = PropertyCharacteristicValue::find($id);

        return view('admin.propertycharacteristicvalue.edit', compact('propertycharacteristicvalue'));
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
            'name' => ['required'],
            'group_name' => ['required'],
            'good' => ['required','numeric'],
            'average' => ['required','numeric'],
            'bad' => ['required','numeric'],
        ]);
        $PropertyCharacteristicValue = PropertyCharacteristicValue::findOrFail($id);
        $PropertyCharacteristicValue->good = $request->good;
        $PropertyCharacteristicValue->average = $request->average;
        $PropertyCharacteristicValue->bad = $request->bad;


        $PropertyCharacteristicValue->save();

        return redirect()->back()->with('success', 'Property Characteristic Value successfully updated');
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
            $district = PropertyCharacteristicValue::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.propertycharacteristic.index')->with('success', 'Property Characteristic  Value Deleted Successfully !');
        } else {
            return Redirect()->route('admin.propertycharacteristic.index')->with('error', "You can't delete Property Characteristic  Value. ");
        }
    }
}
