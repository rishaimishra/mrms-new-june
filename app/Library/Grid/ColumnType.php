<?php


namespace App\Library\Grid;

use Illuminate\Support\Str;

class ColumnType
{
    const BOOLEAN = 'boolean';

    public static function boolean($item, $field)
    {
        $value = $item->{$field};

        $func = 'getBool' . ucfirst(Str::camel($field)) . 'Options';

        if (method_exists($item, $func)) {
            $value = $item->{$func}()[$value];
        } else {
            $value = $value ? __('Published') : __('Draft');
        }

        return "<span class='status--" . ($item->{$field} ? 'process' : 'denied') . "'>" . ($value) . "</span>";
    }
}
