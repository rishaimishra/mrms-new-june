<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const MESSAGE_SUCCESS = 1;
    const MESSAGE_ERROR = 2;

    protected function setMessage($message, $type = 1)
    {
        return [
            'alert' => [
                'type' => $type,
                'msg' => $message
            ]
        ];
    }


    public function resizeImage($file, $putIn, $size = 800)
    {
        $path = $file->hashName($putIn);

        $image = Image::make($file);

        $width = $image->getWidth() > $image->getHeight() ? $size : null;
        $height = $image->getWidth() <= $image->getHeight() ? $size : null;

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->save();

        \Storage::disk('public')->put($path, (string)$image->encode());

        return $path;
    }


    public function storeVideo($file, $putIn, $video)
    {
        $path = $file->hashName($putIn);

        \Storage::disk('public')->put($path , file_get_contents($video));
        
        return $path;

    }
}
