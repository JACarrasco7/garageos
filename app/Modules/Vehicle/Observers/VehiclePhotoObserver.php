<?php

namespace App\Modules\Vehicle\Observers;

use App\Modules\Vehicle\Models\VehiclePhoto;
use Illuminate\Support\Facades\Storage;

class VehiclePhotoObserver
{
    public function created(VehiclePhoto $photo): void
    {
        $this->generateThumbnail($photo);
    }

    public function updated(VehiclePhoto $photo): void
    {
        if ($photo->wasChanged('file_path')) {
            $this->generateThumbnail($photo);
        }
    }

    private function generateThumbnail(VehiclePhoto $photo): void
    {
        if (! Storage::disk('public')->exists($photo->file_path)) {
            return;
        }

        $sourcePath = Storage::disk('public')->path($photo->file_path);
        $info = getimagesize($sourcePath);

        if ($info === false) {
            return;
        }

        [$width, $height] = $info;
        $thumbWidth = 300;
        $thumbHeight = 300;

        $sourceImage = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if ($sourceImage === null) {
            return;
        }

        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresampled(
            $thumbImage,
            $sourceImage,
            0, 0, 0, 0,
            $thumbWidth,
            $thumbHeight,
            $width,
            $height
        );

        $path = pathinfo($photo->file_path, PATHINFO_DIRNAME);
        $filename = pathinfo($photo->file_path, PATHINFO_FILENAME);
        $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);

        $thumbPath = "{$path}/{$filename}-thumb.{$extension}";
        $thumbFullPath = Storage::disk('public')->path($thumbPath);

        match ($info['mime']) {
            'image/jpeg' => imagejpeg($thumbImage, $thumbFullPath, 80),
            'image/png' => imagepng($thumbImage, $thumbFullPath),
            'image/webp' => imagewebp($thumbImage, $thumbFullPath, 80),
            default => null,
        };

        imagedestroy($sourceImage);
        imagedestroy($thumbImage);
    }
}
