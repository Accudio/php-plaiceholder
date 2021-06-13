<?php
/**
 * PHP-Plaiceholder Strategies - Blurhash
 *
 * @see https://blurha.sh/
 *
 * @package    Plaiceholder
 * @subpackage Base64
 */

namespace accudio\Plaiceholder;

class Blurhash {
  /**
   * generate
   *
   * @param  Imagick $image
   *
   * @return string Blurhash string
   */
  public static function generate($image)
  {
    $pixels = Util::get_pixel_array($image, function($colour) {
      return [
        $colour['r'],
        $colour['g'],
        $colour['b']
      ];
    });

    $blurhash = \kornrunner\Blurhash\Blurhash::encode($pixels);
    return $blurhash;
  }
}
