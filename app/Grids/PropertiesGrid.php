<?php

namespace App\Grids;

use App\Models\User;
use Closure;
use Illuminate\Support\HtmlString;
use Leantony\Grid\Grid;

class PropertiesGrid extends Grid implements PropertiesGridInterface
{
    /**
     * The name of the grid
     *
     * @var string
     */
    protected $name = 'Properties';

    /**
     * List of buttons to be generated on the grid
     *
     * @var array
     */
    protected $buttonsToGenerate = [];

    public function __construct()
    {
        if (getPropertyWardPermission('property_view') OR \Auth::user()->super_admin == 1) {
            $this->buttonsToGenerate[] = 'view';
        }

        if (getPropertyWardPermission('property_delete') OR \Auth::user()->super_admin == 1) {
            $this->buttonsToGenerate[] = 'delete';
        }
    }

    // protected $buttonsToGenerate = [
    //     'delete',
    //     'view'
    // ];

    /**
     * Specify if the rows on the table should be clicked to navigate to the record
     *
     * @var bool
     */
    protected $linkableRows = false;

    /**
     * Set the columns to be displayed.
     *
     * @return void
     * @throws \Exception if an error occurs during parsing of the data
     */
    public function setColumns()
    {
        $this->columns = [
            "id" => [
                "label" => "ID",
                "styles" => [
                    "column" => "grid-w-10"
                ],
                'presenter' => function ($columnData, $columnName) {
                    return new HtmlString(
                        '<div class="demo-checkbox">
                        <input type="checkbox" name="selected_properties[]" class="filled-in filtered-property" value="' . $columnData->id . '" id="basic_checkbox_' . $columnData->id . '"  />
                                <label for="basic_checkbox_' . $columnData->id . '">' . $columnData->id . 
                                // ($columnData->created_from ? '/' . $columnData->created_from : '') . 
                                '</label>
                                 <input type="hidden" name="property_ids[]" id="property_ids_current_page" value="' . $columnData->id . '" />
                            
                                </div>'
                    );
                },
            ],
            "landlord" => [
                "search" => [
                    "enabled" => true
                ],
                'presenter' => function ($columnData, $columnName) {
                    return (($columnData->organization_name && $columnData->is_organization == 1)  ? $columnData->organization_name : (@$columnData->landlord->first_name . ' ' . @$columnData->landlord->middle_name . ' ' . @$columnData->landlord->surname));
                },
            ],
            "app_user" => [
                "sort" => false,
                "search" => [
                    "enabled" => true
                ],
                'presenter' => function ($columnData, $columnName) {
                    return optional($columnData->user)->name;
                },
            ],
            "section" => [
                "search" => [
                    "enabled" => true
                ],
            ],
            "chiefdom" => [
                "search" => [
                    "enabled" => true
                ],

            ],
            "province" => [
                "search" => [
                    "enabled" => true
                ],

            ],
            "postcode" => [
                "search" => [
                    "enabled" => true
                ],

            ],
            "is_completed" => [
                'label' => "Completed",
                "search" => [
                    "enabled" => true
                ],
                'presenter' => function ($columnData, $columnName) {
                    return (($columnData->is_completed && $columnData->is_draft_delivered) ? 'Yes' : 'No');
                },

            ],
            "assessment" => [
                "search" => [
                    "enabled" => true
                ],
                'presenter' => function ($columnData, $columnName) {
                    return 'NLe ' . number_format(@$columnData->assessment->property_rate_without_gst, 0, '', ',');
                },
            ],
            "ampunt_paid" => [
                "search" => [
                    "enabled" => true
                ],
                'presenter' => function ($columnData, $columnName) {
                    $sum=0;
                    foreach($columnData->payments as $row){
                        $sum += $row->total;
                    }
                    return 'NLe ' . number_format(@$sum, 0, '', ',');
                },
            ],
            "created_at" => [
                "sort" => false
            ],
            "updated_at" => [
                "sort" => false
            ]
        ];
    }

    /**
     * Set the links/routes. This are referenced using named routes, for the sake of simplicity
     *
     * @return void
     */
    public function setRoutes()
    {
        // searching, sorting and filtering
        $this->setIndexRouteName('admin.properties');

        // crud support
        $this->setCreateRouteName('admin.properties.create');
        $this->setViewRouteName('admin.properties.show');
        $this->setDeleteRouteName('admin.properties.destroy');

        // default route parameter
        $this->setDefaultRouteParameter('id');
    }

    /**
     * Return a closure that is executed per row, to render a link that will be clicked on to execute an action
     *
     * @return Closure
     */
    public function getLinkableCallback(): Closure
    {
        return function ($gridName, $item) {
            return route($this->getViewRouteName(), [$gridName => $item->id]);
        };
    }

    /**
     * Configure rendered buttons, or add your own
     *
     * @return void
     */
    public function configureButtons()
    {



        // call `addRowButton` to add a row button
        // call `addToolbarButton` to add a toolbar button
        // call `makeCustomButton` to do either of the above, but passing in the button properties as an array

        // call `editToolbarButton` to edit a toolbar button
        // call `editRowButton` to edit a row button
        // call `editButtonProperties` to do either of the above. All the edit functions accept the properties as an array
    }

    /**
     * Returns a closure that will be executed to apply a class for each row on the grid
     * The closure takes two arguments - `name` of grid, and `item` being iterated upon
     *
     * @return Closure
     */
    public function getRowCssStyle(): Closure
    {
        return function ($gridName, $item) {
            // e.g, to add a success class to specific table rows;
            // return $item->id % 2 === 0 ? 'table-success' : '';
            return "";
        };
    }
    
}
