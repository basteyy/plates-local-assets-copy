# plates-local-assets-copy

A plugin for pure php based template engine [plates](https://platesphp.com/) which downloads remote assets to local cache

## Setup

First download the library via composer:

```bash
composer require basteyy/plates-local-assets-copy
```

Register the plugin in Plates:

```php
use League\Plates\Engine;
use basteyy\PlatesLocalAssetsCopy\PlatesLocalAssetsCopy as PlatesLocalAssetsCopy;

$templateEngine = new Engine();
$templateEngine->loadExtension(new PlatesLocalAssetsCopy(
    __DIR__ . '/cache/', // Define where the file is stored
    '/public/path') // Define the public access to the file 
);
```

## Usage

Create the hash of a password

```php
// Inside your template-file:
<?= $this->cacheLocal('https://example.com/file') ?>
```

### Example

```php
// Controller for example
use League\Plates\Engine;
use basteyy\PlatesLocalAssetsCopy\PlatesLocalAssetsCopy as PlatesLocalAssetsCopy;

$templateEngine = new Engine();
$templateEngine->loadExtension(new PlatesLocalAssetsCopy(
    __DIR__ . '/cache/', // Define where the file is stored
    '/public/path') // Define the public access to the file 
);
```

```php
// Inside your template-file:
<?= $this->cacheLocal('https://cdnjs.cloudflare.com/ajax/libs/mini.css/3.0.1/mini-default.min.css') ?>
```

File will be downloaded to cache/mini-default.min.css. Inside the template the public path will displayed: /public/path/mini-default.min.css


## License

The MIT License (MIT). Please see [License File](https://github.com/basteyy/plates-local-assets-copy/blob/master/LICENSE) for more information.
