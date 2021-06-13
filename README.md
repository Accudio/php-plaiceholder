<h1 align="center">
  PHP plaiceholder
</h1>

<p align="center">
  <strong>Beautiful image placeholders, without the hassle.</strong>
</p>
<p align="center">
  Choose-your-own adventure, from pure CSS to SVG…
</p>

---

## Table of Contents

1. [Introdution](#introduction)
1. [Installation](#installation)
1. [Setup](#setup)
1. [FAQs](#faqs)

---

## Introduction

[Plaiceholder](https://plaiceholder.co/) is a Node.js utility for generating low quality image placeholders. This package is a re-write in PHP and distributed via composer.

PHP plaiceholder broadly matches the original JS implementation but implements it in pure PHP and hence has a different syntax. This readme will only cover differences from the JS implementation.

For information about the strategies available, their pros and cons, and information about the original project check out the [Plaiceholder docs](https://plaiceholder.co/docs).


## Installation

PHP Plaiceholder should ideally be installed with composer:

```
composer require accudio/plaiceholder
```

If using composer, make sure your application includes `require_once 'vendor/autoload.php';`.

You can alternatively download the repo and `require` the appropriate files from `src/` before creating an instance.


## Setup

Whichever strategy you use, you will first need to create an instance of plaiceholder for your image:

```php
$image_path = '/path/to/your/image.jpg';
$plaiceholder = new accudio\Plaiceholder\Plaiceholder($image_path);
```

The Plaiceholder object accepts an absolute path to the file as stored on your server. ***PHP Plaiceholder does not current support remote images***.

### CSS

CSS strategy has three output modes:

- `style` (default) &mdash; Returns CSS properties ready to be inserted into a style attribute;
- `properties` &mdash; Returns custom properties with values, useful for greater control over where they are applied;
- `array` &mdash; Returns associative array indexed by property name, for the most control.

[CSS strategy on plaiceholder docs](https://plaiceholder.co/docs/usage#css).

```php
$css_style = $plaiceholder->get_css();
// background-image: linear-gradient(...); background-position: 0 0,0 50%,0 100%;
// background-size:100% 33.33%; background-repeat:no-repeat;

$css_properties = $plaiceholder->get_css('properties');
// --plaice-image: linear-gradient(...); --plaice-position: 0 0,0 50%,0 100%;
// --plaice-size:100% 33.33%; --plaice-repeat:no-repeat;

$css_array = $plaiceholder->get_css('array');
// [
//   'background-image'    => 'linear-gradient(...)',
//   'background-position' => '0 0,0 50%,0 100%',
//   'background-size'     => '100% 33.33%',
//   'background-repeat'   => 'no-repeat'
// ]
```

### SVG

SVG returns a string of SVG markup. By default it will include a style attribute with absolute positioning and centering. Pass `false` as a first argument to prevent output of inline styles to do so yourself.

[SVG strategy on plaiceholder docs](https://plaiceholder.co/docs/usage#svg).

```php
$svg_with_styles = $plaiceholder->get_svg();
// <svg xmlns="http://www.w3.org/2000/svg"
//   style="
//     position: absolute;
//     top: 50%;
//     left: 50%;
//     transform-origin: top left;
//     transform: translate(-50%, -50%);
//     right: 0;
//     bottom: 0"
//   width="100%" height="100%"
//   shaperendering="crispEdges" preserveAspectRatio="none"
//   viewBox="0 0 4 3"
// >
//   <rect fill="rgb(155,104,152)" fill-opacity="1" x="0" y="0" width="1" height="1">
//   ...
// </svg>

$svg_no_styles = $plaiceholder->get_svg(false);
// <svg xmlns="http://www.w3.org/2000/svg"
//   width="100%" height="100%"
//   shaperendering="crispEdges" preserveAspectRatio="none"
//   viewBox="0 0 4 3"
// >
//   <rect fill="rgb(155,104,152)" fill-opacity="1" x="0" y="0" width="1" height="1">
//   ...
// </svg>
```

### Base64

Generates low-resolution image and encodes as base64, include data-uri and format. Ready for inserting into `src` attribute or `url()` css function.

[Base64 strategy on plaiceholder docs](https://plaiceholder.co/docs/usage#base64).

**Note:** Due to a difference in the image library used between plaiceholder and PHP Plaiceholder (sharp vs ImageMagick), the base64 strategy doesn't look the same. Whereas plaiceholder tweaks the saturation to make the generated image look slightly better, ImageMagick seems to do fairly well with no tweaking so I've not made changes. Any feedback is appreciated.

```php
$base64 = $plaiceholder->get_base64();
// data:image/jpeg;base64,...
```

### Blurhash

Creates and returns Blurhash string using [php-blurhash by kornrunner](https://github.com/kornrunner/php-blurhash).

[Blurhash strategy on plaiceholder docs](https://plaiceholder.co/docs/usage#blurhash).

**Note:** Due to a difference in blurhash encoder between plaiceholder and PHP Plaiceholder, the blurhash strategy can look slightly different in some cases. I am not familiar with blurhash but if someone who is more knowledgeable here can contribute please do.

```php
$blurhash = $plaiceholder->get_blurhash();
// UqF}a5-WR*xw~E$+WBt8-DxHWBa$$-xHWBai
```

## FAQs

- [What about remote images?](#what-about-remote-images)
- [Is there a plugin for xyz?](#is-there-a-plugin-for-xyz)

### What about remote images?

Currently PHP Plaiceholder does not support remote images. As PHP pages are generally generated when requested rather than in advance it wouldn't be great practice to make a network request in order to generate placeholders. I would suggest you use local images or run store plaiceholder results in a database for later use. That said, if you have a need for it then let me know and I can consider adding - or make a pull request.

### Is there a plugin for XYZ?

There are currently no plugins for PHP CMS' or otherwise. I would be keen for plugins for Craft CMS and perhaps WordPress, but not at this time.

PHP Plaiceholder is fairly simple so likely able to integrate with most platforms fairly easily. I encourage you to make your own integrations, or feel free to build and release plugins or libraries on top of PHP Plaiceholder.
