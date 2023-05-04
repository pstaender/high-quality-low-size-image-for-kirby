<?php

use Kirby\Cms\File;

function high_quality_and_low_size_image(File $file)
{
    if (!str_starts_with($file->mime(), 'image/')) {
        return $file;
    }

    $requestSupports = function ($mime) {
        return str_contains(strtolower(
            kirby()->request()->headers()['Accept'] ?? ''
        ), strtolower($mime));
    };

    $format = option('high_quality_and_low_size_image.format');

    /* To use aviv, ensure Imagick is installed and set in config.php:
        [
        'thumbs' => [
            'driver' => 'im',
        ],
        'high_quality_and_low_size_image' => [
            'format' => 'avif',
        ]
    */

    if (empty($format)) {
        if ($requestSupports('image/webp')) {
            $format = 'webp';
        } else if ($requestSupports('image/avif')) {
            // why is avif 2nd choice? It's not supported in PHP/lib-side. See: https://github.com/claviska/SimpleImage/issues/260
            $format = 'avif';
        }
    }

    // TODO: check that php can create avif/webp (gd|im)
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
    $originalImageTag ??= Kirby\Text\KirbyTag::$types['image'];

    Kirby\Text\KirbyTag::$types['image'] = [
        'attr' => $originalImageTag['attr'],
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

