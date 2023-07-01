# Kirby plugin that converts images to low file-size and high-quality images in avif and webp

## Install

    $ composer require pstaender/high-quality-low-size-image-for-kirby

## Usage

```php
<?= $page->someImage()->toFile()->highQualityLowSize() ?>
```

Now your image will be a webp or avif instead of jpg/png/etc 🚀

Webp will be the format if gdlib is available, avif if imagemagick is enabled via thumb driver.

It also checks that the browser supports webp and avif via the accept header and returns the original file if not supported.

## Optional: Image Tag

To use it also in kirby text (via image tag) set in `config.php`:

```php
[
    'high_quality_and_low_size_image' => [
        'image_tag' => true,
    ],
]
```

You can exclude specific image formats from encoding, by default `gif`, `webp` and `avif` are excluded. To set your own rules or to simply force re-encoding every file (by defining an empty array), set the values here:

```php
[
    'high_quality_and_low_size_image' => [
        'excluded_image_formats' => [
            // 'avif', 'webp', …
        ],
    ],
]
```

## License

MIT
