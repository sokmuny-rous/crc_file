<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileThumbnailService
{
    public function generateFromFirstPage(string $storedPath, ?string $mimeType = null): ?string
    {
        if (! class_exists(\Imagick::class)) {
            return null;
        }

        if (! Storage::disk('local')->exists($storedPath)) {
            return null;
        }

        $absolutePath = Storage::disk('local')->path($storedPath);
        $thumbnailPath = 'thumbnails/'.Str::uuid().'.jpg';

        try {
            $imagick = new \Imagick();

            if (Str::startsWith((string) $mimeType, 'application/pdf')) {
                $imagick->setResolution(130, 130);
                $imagick->readImage($absolutePath.'[0]');
            } elseif (Str::startsWith((string) $mimeType, 'image/')) {
                $imagick->readImage($absolutePath);
            } else {
                return null;
            }

            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(82);
            $imagick->thumbnailImage(900, 0, true);

            Storage::disk('local')->put($thumbnailPath, $imagick->getImageBlob());

            $imagick->clear();
            $imagick->destroy();

            return $thumbnailPath;
        } catch (\Throwable) {
            return null;
        }
    }
}
