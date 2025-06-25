<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\EdsaTransaction;
use Illuminate\Http\Request;
use PDF;


class EdsaTransactionController extends AdminController
{


    public function index()
    {
        $transactions = EdsaTransaction::where('delete_bit','0')->with('user')->get();
        foreach ($transactions as $transaction) {
            // Assuming the response is stored in a field called 'response' (modify this as needed)
            $decodedResponse = json_decode($transaction->response, true); // Decodes to associative array
            
            // Merge decoded data into the transaction object (optional, for convenience)
            if ($decodedResponse) {
                $transaction->meter_number = $decodedResponse['result']['successResponse']['meterNumber'] ?? null;
                $transaction->name = $decodedResponse['result']['successResponse']['voucher']['customer'] ?? null;
                $transaction->reciept = $decodedResponse['result']['successResponse']['voucher']['receiptNumber'] ?? null;
                $transaction->cus_account_number = $decodedResponse['result']['successResponse']['voucher']['accountNumber'] ?? null;
                $transaction->Units = $decodedResponse['result']['successResponse']['voucher']['units'] ?? null;
                $transaction->TransactionAmount = $decodedResponse['result']['successResponse']['amount'] ?? null;
               //  $transaction->DealerBalance = $decodedResponse['result']['data']['Data'][0]['DealerBalance'] ?? null;
                $transaction->CostOfUnits = $decodedResponse['result']['successResponse']['voucher']['costOfUnits'] ?? null;
                $transaction->Tariff = $decodedResponse['result']['successResponse']['voucher']['tariff'] ?? null;
                $transaction->ServiceCharge = $decodedResponse['result']['successResponse']['voucher']['serviceCharge'] ?? null;
                $transaction->TaxCharge = $decodedResponse['result']['successResponse']['voucher']['taxCharge'] ?? null;
                $transaction->debitRecovery = $decodedResponse['result']['successResponse']['voucher']['debitRecovery'] ?? '0.00';
                $transaction->meterToken1 = $decodedResponse['result']['successResponse']['voucher']['meterToken1'] ?? null;
                $transaction->transactionId = $decodedResponse['result']['successResponse']['transactionId'] ?? null;
                //  $transaction->token = $decodedResponse['result']['data']['Data'][0]['PINNumber'] ?? null;
            }
        }
        return view('admin.edsatransaction.grid', compact('transactions'));
    }

    public function downloadPdf($id)
    {
        $transaction = EdsaTransaction::with('user')->findOrFail($id);
        $decodedResponse = json_decode($transaction->response, true); // Decodes to associative array
            
        // Merge decoded data into the transaction object (optional, for convenience)
        if ($decodedResponse) {
            $transaction->meter_number = $decodedResponse['result']['successResponse']['meterNumber'] ?? null;
            $transaction->name = $decodedResponse['result']['successResponse']['voucher']['customer'] ?? null;
            $transaction->reciept = $decodedResponse['result']['successResponse']['voucher']['receiptNumber'] ?? null;
            $transaction->cus_account_number = $decodedResponse['result']['successResponse']['voucher']['accountNumber'] ?? null;
            $transaction->Units = $decodedResponse['result']['successResponse']['voucher']['units'] ?? null;
            $transaction->TransactionAmount = $decodedResponse['result']['successResponse']['amount'] ?? null;
           //  $transaction->DealerBalance = $decodedResponse['result']['data']['Data'][0]['DealerBalance'] ?? null;
            $transaction->CostOfUnits = $decodedResponse['result']['successResponse']['voucher']['costOfUnits'] ?? null;
            $transaction->Tariff = $decodedResponse['result']['successResponse']['voucher']['tariff'] ?? null;
            $transaction->ServiceCharge = $decodedResponse['result']['successResponse']['voucher']['serviceCharge'] ?? null;
            $transaction->TaxCharge = $decodedResponse['result']['successResponse']['voucher']['taxCharge'] ?? null;
            $transaction->debitRecovery = $decodedResponse['result']['successResponse']['voucher']['debitRecovery'] ?? '0.00';
            $transaction->meterToken1 = $decodedResponse['result']['successResponse']['voucher']['meterToken1'] ?? null;
            $transaction->transactionId = $decodedResponse['result']['successResponse']['transactionId'] ?? null;
                //  $transaction->token = $decodedResponse['result']['data']['Data'][0]['PINNumber'] ?? null;
        }
        // return view('admin.edsatransaction.pdf', compact('transaction'));
        $pdf = PDF::loadView('admin.edsatransaction.pdf', compact('transaction','id'));

        return $pdf->download('transaction_' . $id . '.pdf');
    }
}