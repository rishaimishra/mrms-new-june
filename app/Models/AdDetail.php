<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Trails\HasUuid;
use Illuminate\Notifications\Notifiable;


class AdDetail extends Model
{
    use Notifiable;

    protected $fillable = ['ad_name','ad_image','ad_link','status','ad_type','ad_category','ad_content_type','ad_video', 'ad_description','sequence'];



}
