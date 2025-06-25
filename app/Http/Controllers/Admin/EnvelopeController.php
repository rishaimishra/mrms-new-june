<?php

namespace App\Http\Controllers\Admin;

use App\Grids\AdminUsersGrid;
use App\Grids\EnvelopesGrid;
use App\Models\AdminUser;
use App\Models\District;
use App\Models\Property;
use App\Rules\Name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class EnvelopeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = District::query();
        if (request()->user()->hasRole('Super Admin')) {

        } else {
            $query = $query->where('id', request()->user()->assign_district_id);
        }
        //dd(District::where('id',13)->get());
        return (new EnvelopesGrid())
            ->create(['query' => $query, 'request' => $request])
            ->withoutSearchForm()
            ->renderOn('admin.envelope.index');
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

        $district = District::find($id);

        return view('admin.envelope.edit', compact('district'));
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
            'council_name_envp' => ['required', 'max:150'],
            'council_short_name_envp' => ['required', 'max:70'],
            'council_address_envp' => ['required', 'max:70'],
        ]);
        $district = District::findOrFail($id);
        $district->council_name_envp = $request->council_name_envp;
        $district->council_short_name_envp = $request->council_short_name_envp;
        $district->council_address_envp = $request->council_address_envp;
        $district->council_address_envp2 = $request->council_address_envp2;
        $district->council_address_envp3 = $request->council_address_envp3;
        $district->council_address_envp4 = $request->council_address_envp4;
        $district->council_address_envp5 = $request->council_address_envp5;


        if ($request->hasFile('primary_logo_envp')) {
            $district->primary_logo_envp = $request->primary_logo_envp->store(District::IMAGE_PATH);
        }

        if ($request->hasFile('secondary_logo_envp')) {
            $district->secondary_logo_envp = $request->secondary_logo_envp->store(District::IMAGE_PATH);
        }

        // if ($request->hasFile('chif_administrator_sign')) {
        //     $district->chif_administrator_sign = $request->chif_administrator_sign->store(District::IMAGE_PATH);
        // }

        // if ($request->hasFile('ceo_sign')) {
        //     $district->ceo_sign = $request->ceo_sign->store(District::IMAGE_PATH);
        // }

        $district->save();

        return redirect()->back()->with('success', 'District successfully updated');
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
            $district = District::findOrFail($request->district);
            $district->delete();
            return Redirect()->route('admin.envelopes.index')->with('success', 'District Deleted Successfully !');
        } else {
            return Redirect()->route('admin.envelopes.index')->with('error', "You can't delete district. ");
        }
    }
}
