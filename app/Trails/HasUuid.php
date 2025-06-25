<?php
/**
 * Created by PhpStorm.
 * User: Kamlesh
 * Date: 4/9/2019
 * Time: 11:41 AM
 */

namespace App\Trails;

use Webpatser\Uuid\Uuid;

trait HasUuid
{
    public static function bootHasUUID()
    {
        static::creating(function ($model) {
            $uuidFieldName = $model->getUUIDFieldName();
            if (empty($model->$uuidFieldName)) {
                $model->$uuidFieldName = static::generateUUID();
            }
        });
    }

    public function getUUIDFieldName()
    {
        if (!empty($this->uuidFieldName)) {
            return $this->uuidFieldName;
        }
        return 'id';
    }

    public static function generateUUID()
    {
        return Uuid::generate()->string;
    }

    public function scopeByUUID($query, $uuid)
    {
        return $query->where($this->getUUIDFieldName(), $uuid);
    }

    public static function findByUuid($uuid)
    {
        return static::byUUID($uuid)->first();
    }
}
