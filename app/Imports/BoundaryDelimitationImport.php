<?php

namespace App\Imports;

//use App\BoundaryDelimitation;
use App\Models\BoundaryDelimitation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class BoundaryDelimitationImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    // public function model(array $row)
    // {
    //     if (!isset($row[0])) {
    //         return null;
    //     } else {
    //         dd($row);
    //         return new BoundaryDelimitation([
    //             //
    //         ]);
    //     }
    // }

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            if ($row[0] == "Prefix") {
            } elseif (!isset($row[0])) {
            } else {
                // $result = [
                //     'ward' => $row[6],
                //     'constituency' => $row[5],
                //     'section' => $row[4],
                //     'chiefdom' => $row[3],
                //     'district' => $row[2],
                //     'province' => $row[1],
                //     'council' => $row[7],
                //     'prefix' => $row[0],
                // ];
                BoundaryDelimitation::create([
                    'ward' => $row[6] ?: 0,
                    'constituency' => $row[5] ?: 0,
                    'section' => $row[4] ?: 0,
                    'chiefdom' => $row[3] ?: 0,
                    'district' => $row[2] ?: 0,
                    'province' => $row[1] ?: 0,
                    'council' => $row[7] ?: 0,
                    'prefix' => $row[0] ?: 0,
                ]);
            }
        }
    }
}
