# Converts images to low file size high quality images in webp and avif

## Install

    $ composer require pstaender/high-quality-low-size-image-for-kirby

## Usage

```php
<?= $page->image()->toFile()->highQualityLowSize() ?>
```

Optional: To use it also in kirby text (via image tag), set in `config.php`:

```php
[
    'high_quality_and_low_size_image' [
        'image_tag' => true,
    ],
]
```

## License

MIT
