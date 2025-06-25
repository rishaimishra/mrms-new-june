<?php

namespace App\Http\Controllers\Admin;

use App\Grids\TaxPayerGrid;
use App\Library\Grid\Grid;
use App\Models\LandlordDetail;
use Illuminate\Http\Request;

class TaxPayerController extends AdminController
{
    public function index(TaxPayerGrid $grid, Request $request)
    {
        $query = LandlordDetail::whereHas('property')->groupBy('mobile_1')
            ->join('properties', 'properties.id', 'landlord_details.property_id')
            ->select()
            ->addSelect(\DB::raw('COUNT(properties.id) as properties_count'))
            ->whereNotNull('mobile_1');

        $grid = (new Grid())
            ->setQuery($query)
            ->setDefaultSortColumn('properties.created_at')
            ->setColumns([
                [
                    'field' => 'first_name',
                    'label' => 'First Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'surname',
                    'label' => 'Last Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'properties_count',
                    'label' => 'Total Properties',
                    'sortable' => true,
                ],
            ])->setButtons([
                [
                    'label' => 'See Properties',
                    'url' => function($item) {
                        return route('admin.properties', ['mobile' => $item->mobile_1]);
                    }
                ]
            ]);

        return view('admin.report.tax-payers', compact('grid'));
    }
}
