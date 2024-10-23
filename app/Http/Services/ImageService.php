<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function storeImage($image, $folder)
    {
        return $image->store($folder, 's3');
    }

    public function getTemporaryImageUrl($path)
    {
        return $path ? Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60)) : null;
    }

    public function deleteImage($path)
    {
        return $path ? Storage::disk('s3')->delete($path) : false;
    }
}
