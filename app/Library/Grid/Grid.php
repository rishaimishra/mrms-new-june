<?php

namespace App\Library\Grid;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Grid
{
    protected $queryBuilder;
    protected $columns;
    protected $perPage = 20;
    protected $buttons;

    public function setQuery(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    public function setColumns(array $columns)
    {
        $this->columns = collect($columns);
        return $this;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
        return $this;
    }

    protected function getColumns()
    {
        $columns = $this->columns->map(function ($column) {

            $column['orig_label'] = $column['label'];

            if ($column['sortable'] ?? false) {
                $column['label'] = $this->applySortingAttributes($column['label'], $column['field']);
            }

            return $column;
        });

        return $columns;
    }

    protected function applyFilters()
    {
        $filterable = $this->columns->filter(function ($item) {
            return isset($item['filterable']);
        });


        foreach ($filterable as $item) {

            $inputKey = 'grid.filter.' . $item['field'];
            $filledValue = request()->filled($inputKey);
            $value = request($inputKey);

            if (isset($item['filterable']['callback']) && is_callable($item['filterable']['callback'])) {

                if ($filledValue) {
                    $item['filterable']['callback']($this->queryBuilder, $value);
                }

                continue;
            }

            $type = $item['filterable']['type'] ?? 'like';


            if ($filledValue) {
                switch ($type) {
                    case 'eq':
                        {

                            break;
                        }
                    case 'like':
                        {
                            $this->queryBuilder->where($item['field'], 'like', "%{$value}%");
                            break;
                        }
                }
            }
        }
    }

    protected function applySorting()
    {
        $field = request('grid.sort.field');
        $order = request('grid.sort.order');

        $sortables = $this->columns->where('sortable', true)->pluck('field')->toArray();

        // is valid field
        if (!$field || !$order || !in_array($order, ['asc', 'desc']) || !in_array($field, $sortables)) {
            return;
        }

        $this->queryBuilder->orderBy($field, $order);
    }

    protected function applySortingAttributes($label, $field)
    {
        $inputs = request()->except($field);

        if (request('grid.sort.field') == $field) {
            $inputs['grid']['sort']['order'] = request('grid.sort.order') === 'asc' ? 'desc' : 'asc';
        } else {
            $inputs['grid']['sort']['field'] = $field;
            $inputs['grid']['sort']['order'] = 'asc';
        }

        $params = http_build_query($inputs);
        return link_to(request()->url() . '?' . $params, $label);
    }

    public function generate()
    {
        $this->applySorting();
        $this->applyFilters();

        $data = $this->queryBuilder->paginate($this->perPage);

        $items = [];

        foreach ($data as $item) {

            $temp = [];

            foreach ($this->columns as $column) {

                if ($column['formatter'] ?? false && is_callable($column['formatter'])) {
                    $temp[$column['field']] = $column['formatter']($item->{$column['field']}, $item);
                } else if ($item->{$column['field']} instanceof Carbon) {
                    $temp[$column['field']] = $item->{$column['field']}->toDayDateTimeString();
                }
            }

            foreach ($this->buttons as $button) {
                $temp['_buttons'][] = view('components.grid.button', compact('item', 'button'));
            }

            $temp = array_merge($item->toArray(), $temp);
            $items[] = $temp;
        }

        $columns = $this->getColumns();

        $perPage = $this->perPage;
        $pageStart = (request('page', 1) - 1) * $perPage;
        $buttons = $this->buttons;
        $links = $data->appends(['grid' => request('grid')])->links();

        return view('components.grid.table', \compact(
            'items',
            'columns',
            'pageStart',
            'buttons',
            'links'
        ));
    }
}
