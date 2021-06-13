<?php
/**
 * PHP-Plaiceholder Strategies - Base64
 *
 * @package    Plaiceholder
 * @subpackage Base64
 */

namespace accudio\Plaiceholder;

class Base64 {
  /**
   * generate
   *
   * @param  Imagick $image
   *
   * @return string  Base64 string, including data and format prefix
   */
  public static function generate($image)
  {
    // the original plaiceholder with sharp uses these but with Imagick
    // it results in massive oversaturation
    // $image->normalizeImage();
    // $image->modulateImage(100, 120, 100);

    // remove alpha channel
    $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);

    return sprintf(
      'data:image/%s;base64,%s',
      strtolower($image->getImageFormat()),
      base64_encode($image->getImageBlob())
    );
  }
}
