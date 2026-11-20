<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoredImage
{
    public static function store(UploadedFile $file, string $directory): string
    {
        $source = self::open($file);
        $fitted = self::limitWidth($source, 1200);
        if ($fitted !== $source) {
            imagedestroy($source);
        }

        $name = $directory.'/'.Str::uuid()->toString().'.jpg';
        $absolute = Storage::disk('public')->path($name);
        if (! is_dir(dirname($absolute))) {
            mkdir(dirname($absolute), 0755, true);
        }

        imagejpeg($fitted, $absolute, 82);
        imagedestroy($fitted);

        return $name;
    }

    public static function storeSquare(UploadedFile $file, string $directory, int $size = 512): string
    {
        $source = self::open($file);
        $square = self::square($source, $size);
        if ($square !== $source) {
            imagedestroy($source);
        }

        $name = $directory.'/'.Str::uuid()->toString().'.jpg';
        $absolute = Storage::disk('public')->path($name);
        if (! is_dir(dirname($absolute))) {
            mkdir(dirname($absolute), 0755, true);
        }

        imagejpeg($square, $absolute, 82);
        imagedestroy($square);

        return $name;
    }

    public static function delete(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    private static function open(UploadedFile $file): \GdImage
    {
        $contents = file_get_contents($file->getRealPath());
        $image = @imagecreatefromstring($contents === false ? '' : $contents);
        if ($image === false) {
            abort(422, 'Не удалось прочитать изображение.');
        }

        return $image;
    }

    private static function limitWidth(\GdImage $image, int $maxWidth): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width <= $maxWidth) {
            return $image;
        }

        $targetHeight = (int) round($height * ($maxWidth / $width));
        $resized = imagescale($image, $maxWidth, $targetHeight);
        if ($resized === false) {
            return $image;
        }

        return $resized;
    }

    private static function square(\GdImage $image, int $size): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $side = min($width, $height);
        $crop = imagecrop($image, [
            'x' => intdiv($width - $side, 2),
            'y' => intdiv($height - $side, 2),
            'width' => $side,
            'height' => $side,
        ]);
        if ($crop === false) {
            $crop = $image;
        }

        if (imagesx($crop) === $size && imagesy($crop) === $size) {
            return $crop;
        }

        $resized = imagescale($crop, $size, $size);
        if ($resized === false) {
            return $crop;
        }
        if ($crop !== $image) {
            imagedestroy($crop);
        }

        return $resized;
    }
}
