<?php

namespace App\Models;

use App\Notifications\Admin\PasswordResetNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class AdminUser extends Authenticatable
{

    use Notifiable;
    use HasRoles;
    protected $guard_name = 'admin';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'is_active', 'username', 'media_id', 'position'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getName()
    {
        return ucwords($this->first_name . ' ' . $this->last_name);
    }

    public function credit()
    {
        return $this->morphOne(CreditTransaction::class, 'creditable');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function getImage($width = 100, $height = 100)
    {
        if ($media = $this->media) {

            return $media->getImage($width, $height);

        }

        return null;
    }


    public function posts()
    {
        return $this->hasMany(Post::class, 'admin_user_id');
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'user_id');
    }

}
