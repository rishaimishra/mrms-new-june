<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\StarTransaction;
use Illuminate\Http\Request;
use PDF;

class StarTransactionController extends AdminController
{


    public function index()
    {
        $transactions = StarTransaction::with('user')->get();
        foreach ($transactions as $transaction) {
            // Assuming the response is stored in a field called 'response' (modify this as needed)
            $decodedResponse = json_decode($transaction->response, true); // Decodes to associative array
            
            // Merge decoded data into the transaction object (optional, for convenience)
            if ($decodedResponse) {
                $transaction->product_name = $decodedResponse['content']['transactions']['product_name'] ?? null;
                $transaction->unit_price = $decodedResponse['content']['transactions']['unit_price'] ?? null;
                $transaction->total_amount = $decodedResponse['content']['transactions']['total_amount'] ?? null;
                $transaction->type = $decodedResponse['content']['transactions']['type'] ?? null;
            }
        }
        return view('admin.startransaction.grid', compact('transactions'));
    }

    public function downloadPdf($id)
    {
        $transaction = StarTransaction::with('user')->findOrFail($id);
        $decodedResponse = json_decode($transaction->response, true); // Decodes to associative array
            
        // Merge decoded data into the transaction object (optional, for convenience)
        if ($decodedResponse) {
            $transaction->product_name = $decodedResponse['content']['transactions']['product_name'] ?? null;
            $transaction->unit_price = $decodedResponse['content']['transactions']['unit_price'] ?? null;
            $transaction->total_amount = $decodedResponse['content']['transactions']['total_amount'] ?? null;
            $transaction->type = $decodedResponse['content']['transactions']['type'] ?? null;
        }
        $pdf = PDF::loadView('admin.startransaction.pdf', compact('transaction'));

        return $pdf->download('transaction_' . $id . '.pdf');
    }
}