<?php

declare(strict_types=1);

namespace App\Helper;

final class ConvertImage
{
    public function webpToPNG(string $source_file, string $destination_file, $compression_quality = 100): bool|string
    {
        $image = imagecreatefromwebp($source_file);
        $result = imagepng($image, $destination_file, $compression_quality);
        if (false === $result) {
            return false;
        }
        imagedestroy($image);
        return $destination_file;
    }
}