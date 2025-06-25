<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LegalTermsAndPolicies;

class LegalTermsAndPoliciesController extends AdminController
{
    //
    public function index()
    {
        $legalTermsAndPolicies = LegalTermsAndPolicies::where('app_type', '=', 'user')->first();
        return view('admin.legaltermsandpolicies.legal' ,  compact('legalTermsAndPolicies'));
    }
    public function SellerTerms()
    {
        $legalTermsAndPolicies = LegalTermsAndPolicies::where('app_type', '=' , 'seller')->first();
        return view('admin.legaltermsandpolicies.legalSeller' ,  compact('legalTermsAndPolicies'));
    }

    public function update(Request $request)
    {
        $legalTermsAndPolicies = LegalTermsAndPolicies::first();

        if (!$legalTermsAndPolicies) {
            LegalTermsAndPolicies::create([
                'termsText' => $request->termsText
            ]);
        } else {
            $legalTermsAndPolicies->update([
                'termsText' => $request->termsText
            ]);
        }

        return redirect()->route('admin.legal.index')->with('success', 'Legal Terms updated successfully!');
    }
    public function SellerTermsUpdate(Request $request)
    {
        $legalTermsAndPolicies = LegalTermsAndPolicies::where('app_type', '=' , 'seller')->first();

        if (!$legalTermsAndPolicies) {
            LegalTermsAndPolicies::create([
                'termsText' => $request->termsText
            ]);
        } else {
            $legalTermsAndPolicies->update([
                'termsText' => $request->termsText
            ]);
        }

        return redirect()->route('admin.seller.terms')->with('success', 'Legal Terms updated successfully!');
    }
}
