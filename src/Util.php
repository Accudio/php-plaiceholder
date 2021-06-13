<?php
/**
 * PHP-Plaiceholder Utilities
 *
 * @package    phpplaiceholder
 * @subpackage util
 */

namespace accudio\Plaiceholder;

class Util {
  /**
   * get_pixel_array
   *
   * @param  Imagick  $image  Image loaded from Imagick
   * @param  function $format (optional) Function to alter colour format. Passed 1 argument - `colour` - an array in the format ['r' => 0-255, 'g' => 0-255, 'b' => 0-255, 'a' => 0-1]
   * @return array Nested array of image rows, each an array with pixel colour data
   */
  public static function get_pixel_array($image, $format = false)
  {
    $pixels = [];

    $iterator = $image->getPixelIterator();

    // loop through each row
    foreach ($iterator as $pixel_row) {
      $row = [];

      foreach ($pixel_row as $pixel) {
        $colour = $pixel->getColor();
        if ($format) {
          $colour = $format($colour);
        }
        $row[] = $colour;
      }

      $pixels[] = $row;

      // need to sync iterator each time
      $iterator->syncIterator();
    }

    return $pixels;
  }
}
