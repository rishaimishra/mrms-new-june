<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;

abstract class AdminController extends Controller
{
    protected function guard()
    {
        return \Auth::guard('admin');
    }

    protected function admin(): AdminUser
    {

        return $this->guard()->user();
    }
}