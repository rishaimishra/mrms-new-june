<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Imports\BoundaryDelimitationImport;
use Maatwebsite\Excel\Facades\Excel;

class BoundaryDelimitationController extends AdminController
{
    public function createUpload()
    {

        return view('boundarydelimitation.upload');
    }


    function import(Request $request)
    {

        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx',
        ]);

        Excel::import(new BoundaryDelimitationImport($request), request()->file('select_file'));


        return back()->with('success', 'Excel Data Imported successfully.');
    }
}
