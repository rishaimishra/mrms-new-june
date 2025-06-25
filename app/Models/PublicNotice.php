<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicNotice extends Model
{
    protected $table = 'public_notices';
    protected $fillable = [
        'notice',
        'acronym',
        'description',
        'created_at',
        'updated_at',
    ];
    public function images()
    {
        return $this->HasMany(NoticeImage::class,'notice_id');
    }
}
