<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\AdjustmentValuesGrid;
use App\Models\AdminUser;
use App\Models\AdjustmentValue;
use App\Models\Property;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdjustmentValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AdjustmentValue::query();
        
        //dd(District::where('id',13)->get());
        return (new AdjustmentValuesGrid())
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

        $adjustmentvalue = AdjustmentValue::find($id);

        return view('admin.adjustmentvalue.edit', compact('adjustmentvalue'));
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
            'percentage' => ['required','numeric'],
        ]);
        $adjustmentval = AdjustmentValue::findOrFail($id);
        $adjustmentval->percentage = $request->percentage;


        $adjustmentval->save();

        return redirect()->back()->with('success', 'Adjustment Value successfully updated');
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
            $district = AdjustmentValue::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.envelopes.index')->with('success', 'Adjustment Value Deleted Successfully !');
        } else {
            return Redirect()->route('admin.envelopes.index')->with('error', "You can't delete Adjustment Value. ");
        }
    }
}
