=== vralle.lqip ===
Contributors: @vit-1
Tags: lazy load, lazyload, images, lqip
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: master
Requires at least: 4.9
Requires PHP: 5.6
Tested up to: 5.3

LQIP (Low Quality Image Placeholder) for vralle.lazyload. Fast and lightweight image previews.

== Description ==

LQIP (Low Quality Image Placeholder) for vralle.lazyload. Fast and lightweight image previews.

This project is porting Gaussholder for vralle.lazyload:<br>
[Gaussholder](https://github.com/humanmade/Gaussholder) (GPL 2+)<br>
[vralle.lazyload](https://github.com/vralle/vralle-lazyload) (GPL 2+)

When we met with a Gaussholder, the guys from Human Made did a great job and were able to solve one of the biggest problems - reducing the size of the preview image to bytes, but making it beautiful.

Gaussholder uses its own mechanism for delayed image loading. We use the time-tested [LazySizes] (https://github.com/aFarkas/lazysizes) by Alexander Farkash. The mechanism for creating an image for preview was ported to this plugin, but received some changes.

We use the same filters as the original Gaussholder code.

Our experience: <br>
A study of performance in browsers yielded results when scaling a preview image to the size of the original image could create a critical load on browsers. A high load on the browser creates a blur filter. As a result, changes were made to the source code. Preview images are created as needed when they fall into vieport. Display a small preview image and use the CSS property background-size = "cover" to scale. We no longer use the data-gaussholder-size attribute and blur animations. We suggest using classes gaussholder-loading, gaussholder-loaded, gaussholder-removed and standard lazySizes classes: lazyload, lazyloading and lazyloaded.

== Installation ==

1. Download and activate [vralle.lazyload](https://github.com/vralle/vralle-lazyload).
1. Download and activate the plugin from this repo.

Or install plugins with [GitHub Updater](https://github.com/afragen/github-updater).

== How do I use it? ==

1. Select the image sizes to use LQIP on, and add them to the array on the `gaussholder.image_sizes` filter.
1. If you have existing images, regenerate the image thumbnails.

== Changelog ==

== Upgrade Notice ==
