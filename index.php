<?php

use Kirby\Cms\File;

function high_quality_and_low_size_image(File $file)
{
    if (!str_starts_with($file->mime(), 'image/')) {
        return $file;
    }

    $excludeFormats = option('high_quality_and_low_size_image.excluded_image_formats') ?: ['gif', 'webp', 'avid'];
    $excludeFormats = array_map(function($format) {
        return "image/$format";
    }, $excludeFormats);
    
    if (in_array($file->mime(), $excludeFormats)) {
        return $file;
    }

    // abort if neither gdlib nor imagick is available
    if (!function_exists('gd_info') && !extension_loaded('imagick')) {
        return $file;
    }

    $requestSupports = function ($mime) {
        return str_contains(strtolower(
            kirby()->request()->headers()['Accept'] ?? ''
        ), strtolower($mime));
    };

    $format = option('high_quality_and_low_size_image.format');

    if (empty($format)) {
        // avif is a bit smaller by "equal" quality 
        if ($requestSupports('image/avif')) {
            $format = 'avif';
        }
        else if ($requestSupports('image/webp')) {
            $format = 'webp';
        }
    }

    // we check for avif support if gd is enabled
    if ($format === 'avif' && function_exists('gd_info')) {
        if (!(gd_info()['AVIF Support'] ?? null)) {
            $format = 'webp';
        }
    }

    $imageSize = $file->dimensions()->width() * $file->dimensions()->height();
    // is between 79 and 22
    $quality = round((100 * ((pi() / 2) + atan(((- ($imageSize / 100000) * 0.5) - 40) / 100)) / (3)) * 2);

    return $file->thumb(['format' => $format, 'quality' => $quality]);
}



Kirby::plugin('pstaender/high-quality-low-size-image', [
    'fileMethods' => [
        'highQualityLowSize' => function () {
            return high_quality_and_low_size_image($this);
        },
    ]
]);

/*
 * Make highQualityLowSize also available via image tag in textareas
 */

if (option('high_quality_and_low_size_image.image_tag')) {
    $originalImageTag = Kirby\Text\KirbyTag::$types['image'];

    Kirby\Text\KirbyTag::$types['image'] = [
        'html' => function ($tag) use ($originalImageTag) {
            if ($tag->file = $tag->file($tag->value)) {
                $url     = $tag->file->url();
            } else {
                // we can't proceed here without a file object
                return $originalImageTag['html']($tag);
            }
            $imageTag = (string)$originalImageTag['html']($tag);
            return str_replace($url, $tag->file->highQualityLowSize()->url(), $imageTag);
        },
    ];    
}

