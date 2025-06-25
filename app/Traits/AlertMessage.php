<?php

namespace App\Traits;

trait AlertMessage
{
    public function createResponse(): array
    {
        return alertSuccess(trans('module.created', ['module' => $this->module]));
    }

    public function updateResponse(): array
    {
        return alertSuccess(trans('module.updated', ['module' => $this->module]));
    }

    public function deleteResponse(): array
    {
        return alertSuccess(trans('module.deleted', ['module' => $this->module]));
    }
}
