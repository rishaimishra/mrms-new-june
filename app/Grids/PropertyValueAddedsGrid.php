<?php

namespace App\Grids;

use Closure;
use Leantony\Grid\Grid;

class PropertyValueAddedsGrid extends Grid implements PropertyValueAddedGridInterface
{
    /**
     * The name of the grid
     *
     * @var string
     */
    protected $name = 'PropertyValueAdded';

    /**
     * List of buttons to be generated on the grid
     *
     * @var array
     */
    protected $buttonsToGenerate = [
        'create',
        'view',
        'delete',
        'refresh',
        'export'
    ];

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
                "filter" => [
                    "enabled" => true,
                    "operator" => "="
                ],
                "styles" => [
                    "column" => "grid-w-10"
                ]
            ],
            "label" => [
                "search" => [
                    "enabled" => true
                ],
                "filter" => [
                    "enabled" => true,
                    "operator" => "like"
                ]
            ],
            "value" => [
                "search" => [
                    "enabled" => true
                ],
                "filter" => [
                    "enabled" => true,
                    "operator" => "="
                ]
            ],
            "created_at" => [
                "sort" => false,
                "date" => "true",
                "filter" => [
                    "enabled" => true,
                    "type" => "date",
                    "operator" => "<="
                ]
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
        $this->setIndexRouteName('admin.list.property.value-added');

        // crud support
        $this->setCreateRouteName('admin.create.property.value-added');
        $this->setViewRouteName('admin.create.property.value-added');
        $this->setDeleteRouteName('admin.destroy.property.value-added');

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
        $this->editRowButton('delete', [
            'class' => 'delete-confirm data-remote grid-row-button btn btn-outline-danger btn-sm',
        ]);

        $this->editRowButton('view', [
            'name' => 'Edit',
        ]);

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
