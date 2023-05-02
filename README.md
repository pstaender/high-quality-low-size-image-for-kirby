# Converts images to low file size high quality images in webp and avif

## Install

    $ composer require pstaender/high-quality-low-size-image-for-kirby

## Usage

```php
<?= $page->someImage()->toFile()->highQualityLowSize() ?>
```

Now your image will be a webp or avif instead of jpg/png/etc 🚀. Webp will be the format if gdlib, avif if imagemagick is enabled via thumb driver. It also checks that the browser supports webp and avif via the Accept header and return the original file if not.

## Optional: Image Tag

To use it also in kirby text (via image tag) set in `config.php`:

```php
[
    'high_quality_and_low_size_image' => [
        'image_tag' => true,
    ],
]
```

## License

MIT
