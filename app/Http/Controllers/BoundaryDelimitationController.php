<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoundaryDelimitationController extends Controller
{
    public function createUpload()
    {

        return view('boundarydelimitation.upload');
    }


    function import(Request $request)
    {

        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric',
        ]);
        //dd($request->select_file);
        Excel::import(new QuestionsImport($request), request()->file('select_file'));
        //Excel::import(new QuestionsImport, request()->file('select_file'));

        return back()->with('success', 'Excel Data Imported successfully.');
    }
}
