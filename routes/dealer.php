<?php

use App\Http\Controllers\Admin\Attribute\AttributeSetController;
use Illuminate\Support\Facades\Route;

use PgSql\Lob;


Route::get('loginDealer', 'Dealer\DealerLogingConroller@showForm')->name('loginDealer');