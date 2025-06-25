<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Returns;

class ReturnController extends AdminController
{
    //
    public function index()
    {
        $returns = Returns::first();
        return view('admin.returns.view' ,  compact('returns')); //need to change according to privacy policy
    }

    public function update(Request $request)
    {
        $returns = Returns::first();

        if (!$returns) {
            Returns::create([
                'title' => $request->title
            ]);
        } else {
            $returns->update([
                'title' => $request->title
            ]);
        }

        return redirect()->route('admin.returns.index')->with('success', 'Legal Terms updated successfully!');//need to change according to privacy policy
    }
}

//ReturnController.php