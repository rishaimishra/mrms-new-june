<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\DstvTransaction;
use Illuminate\Http\Request;
use PDF;

class DstvTransactionController extends AdminController
{


    public function index()
    {
        $transactions = DstvTransaction::with('user')->get();
        foreach ($transactions as $transaction) {
            // Assuming the response is stored in a field called 'response' (modify this as needed)
            $decodedResponse = json_decode($transaction->response, true); // Decodes to associative array
            
            // Merge decoded data into the transaction object (optional, for convenience)
            if ($decodedResponse) {
                $transaction->product_name = $decodedResponse['content']['transactions']['product_name'] ?? null;
                $transaction->type = $decodedResponse['content']['transactions']['type'] ?? null;
                $transaction->email = $decodedResponse['content']['transactions']['email'] ?? null;
                $transaction->phone = $decodedResponse['content']['transactions']['phone'] ?? null;
                $transaction->response_description = $decodedResponse['response_description'] ?? null;
            }
        }
        return view('admin.dstvtransaction.grid', compact('transactions'));
    }

    public function downloadPdf($id)
    {
        $transaction = DstvTransaction::with('user')->findOrFail($id);
        // return view('admin.dstvtransaction.pdf', compact('transaction'));
        $decodedResponse = json_decode($transaction->response, true); // Decodes to associative array
            
            // Merge decoded data into the transaction object (optional, for convenience)
            if ($decodedResponse) {
                $transaction->product_name = $decodedResponse['content']['transactions']['product_name'] ?? null;
                $transaction->type = $decodedResponse['content']['transactions']['type'] ?? null;
                $transaction->email = $decodedResponse['content']['transactions']['email'] ?? null;
                $transaction->phone = $decodedResponse['content']['transactions']['phone'] ?? null;
                $transaction->response_description = $decodedResponse['response_description'] ?? null;
            }
            // return view('admin.dstvtransaction.pdf', compact('transaction'));
        $pdf = PDF::loadView('admin.dstvtransaction.pdf', compact('transaction'));

        return $pdf->download('transaction_' . $id . '.pdf');
    }
}