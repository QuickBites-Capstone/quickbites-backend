<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'public');
    }

    public function storeImage($image, $folder)
    {
        return $image->store($folder, $this->disk);
    }

    public function getTemporaryImageUrl($path)
    {
        if (!$path) {
            return null;
        }

        $storage = Storage::disk($this->disk);

        // Try temporary URL for S3, fallback to permanent URL for local
        try {
            return $storage->temporaryUrl($path, now()->addMinutes(60));
        } catch (\RuntimeException $e) {
            // Fallback for local disk: return a direct public URL
            return $storage->url($path);
        }
    }

    public function deleteImage($path)
    {
        return $path ? Storage::disk($this->disk)->delete($path) : false;
    }
}
