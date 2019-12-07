# vralle.lqip

* Requires at least: 4.9
* Requires PHP: 5.6
* Tested up to: 5.3
* Stable tag: [master](https://github.com/afragen/github-updater/releases/latest)
* License: GPLv2 or later
* License URI: <https://www.gnu.org/licenses/gpl-2.0.html>

## Description
LQIP (Low Quality Image Placeholder) for vralle.lazyload. Fast and lightweight image previews.

This project is porting Gaussholder for vralle.lazyload:<br>
[Gaussholder](https://github.com/humanmade/Gaussholder)<br>
[vralle.lazyload](https://github.com/vralle/vralle-lazyload)

When we met with a Gaussholder, the guys from Human Made did a great job and were able to solve one of the biggest problems - reducing the size of the preview image to bytes, but making it beautiful.

Gaussholder uses its own mechanism for delayed image loading. We use the time-tested [LazySizes] (https://github.com/aFarkas/lazysizes) by Alexander Farkash. The mechanism for creating a preview image has been ported to this plugin, but received some changes..

vralle.lqip use the same filter and metadata as Gaussholder.

### Our experience:

A study of performance in browsers yielded results when scaling a preview image to the size of the original image could create a critical load on browsers. A high load on the browser creates a blur filter. As a result, changes were made to the source code. Preview images are created as needed when they fall into viewport. Display a small preview image and use the CSS property background-size = "cover" to scale. We no longer use the data-gaussholder-size attribute and blur animations. We suggest using classes gaussholder-loading, gaussholder-loaded, gaussholder-removed and lazySizes classes: lazyload, lazyloading and lazyloaded.

## Requirement

1. Installed and activated [vralle.lazyload](https://github.com/vralle/vralle-lazyload)
2. PHP PECL Imagick installed on server.

## Installation

1. Download and activate [vralle.lazyload](https://github.com/vralle/vralle-lazyload).
2. Download and activate the plugin from this repo.

### Github Updater

1. Install [github-updater](https://github.com/afragen/github-updater) by downloading the latest zip [here](https://github.com/afragen/github-updater/releases).
2. Install vralle.lazyload by downloading the latest zip [here](https://github.com/vralle/vralle-lazyload/releases).
3. Install vralle.lqip by downloading the latest zip [here](https://github.com/vralle/vralle-lqip/releases).

All plugins will now download their own updates automatically, so you will never need to go through that tedious zip downloading again.

## How do I use it?

1. Select the image sizes to use LQIP on, and add them to the array on the `gaussholder.image_sizes` filter.
2. If you have existing images, regenerate the image thumbnails.

Your filter should look something like this:

```php
add_filter( 'gaussholder.image_sizes', function ( $sizes ) {
	$sizes['medium'] = 16;
	$sizes['large'] = 32;
	$sizes['full'] = 84;
	return $sizes;
} );
```

The keys are registered image sizes, with the value as scaling factor.

By default, the plugin won't generate any placeholders, and you need to opt-in to using it. Simply filter here, and add the size names for what you want generated.

Be aware that for every size you add, a placeholder will be generated and stored in the database. If you have a lot of sizes, this will be a _lot_ of data.

### Scaling factor

The image is pre-scaled down by this factor, and this is really the key to how the placeholders work. Increasing factor decreases the required data quadratically: a factor of 2 uses a quarter as much data as the full image; a factor of 8 uses 1/64 the amount of data. (Due to compression, the final result will *not* follow this scaling.)

Be careful tuning this, as decreasing the factor too much will cause a huge amount of data in the body; increasing it will end up with not enough data to be an effective placeholder.

The factor needs to be tuned to each size individually. Facebook uses about 200 bytes of data for their placeholders, but you may want higher quality placeholders. There's no ideal factor, as you simply want to balance having a useful placeholder with the extra time needed to process the data on the page.

We recommend that you use a factor so that the resulting preview image falls in size 24 * 24 pixels. These sizes are enough to get a blurry image with sizes up to 1000 * 1000 pixels, the size of the stored and transmitted data will not exceed 200 bytes.

Large images create a load on the database, network traffic and can create a critical load in client browsers. Use images only after testing on mobile clients. Be careful.

Note: changing the radius requires regenerating the placeholder data.

## License

Copyright 2019 the Authors.

This project is licensed under the terms of the [GPL 2.0 or later](LICENSE.txt).

Gaussholder is licensed under the [GPLv2 or later](https://github.com/humanmade/Gaussholder/blob/master/LICENSE.md).

StackBlur licensed under the [MIT license](https://github.com/flozz/StackBlur/blob/master/LICENSE-MIT.txt).
