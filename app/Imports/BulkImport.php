<?php
namespace App\Imports;
use App\Bulk;
// use App\Models\Bulk;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class BulkImport implements ToModel,WithHeadingRow
{
	/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Bulk([
            'property_id'     => $row['property_id'],
            'admin_user_id'    => $row['admin_user_id'],
            'assessment'    => $row['assessment'],
            'amount'    => $row['amount'],
            'total'    => $row['total'],
            'penalty'    => null,
            'balance'    => null,
            'payment_type'    => $row['payment_type'],
            'cheque_number'    =>null,
            'transaction_id'    => null,
            'payee_name'    => $row['payee_name'],
            'physical_receipt_image'    => null,
            'pensioner_discount_image'    => null,
            'disability_discount_image'    =>null,
            'created_at'    => $row['created_at'],
            'migrate_at'    => null,
            'updated_at'    => null,
            'deleted_at'    => null,
            'pensioner_discount_approve'    => null,
            'disability_discount_approve'    => null,
            'payment_made_year'    => $row['payment_made_year'],
        ]);


    }
}




