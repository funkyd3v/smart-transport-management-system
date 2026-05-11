<?php

declare(strict_types=1);

$mediaLibraryConfig = require base_path('vendor/spatie/laravel-medialibrary/config/media-library.php');

$hasGd = function_exists('imagecreatefromstring');
$hasImagick = extension_loaded('imagick');

if ($hasImagick) {
    $mediaLibraryConfig['image_driver'] = 'imagick';
} elseif ($hasGd) {
    $mediaLibraryConfig['image_driver'] = 'gd';
}

$mediaLibraryConfig['generate_thumbnails_for_temporary_uploads'] = $hasGd || $hasImagick;

return $mediaLibraryConfig;
