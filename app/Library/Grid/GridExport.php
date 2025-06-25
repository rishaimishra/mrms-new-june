<?php

namespace App\Library\Grid;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GridExport implements FromArray, ShouldAutoSize
{
    protected $data;
    protected $columns;

    public function __construct($data, $columns)
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        $data[] = $this->columns->prepend(['orig_label' => '#'])->map(function ($column) {
            return $column['orig_label'];
        })->toArray();


        foreach ($this->data as $key => $item) {

            $temp[0] = ($key+1);

            foreach ($this->columns as $key => $column) {

                if (isset($column['field'])) {
                    $temp[$key+1] = strip_tags($item[$column['field']]);
                }
            }

            $data[] = $temp;
        }

        return $data;
    }
}
