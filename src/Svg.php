<?php
/**
 * PHP-Plaiceholder Strategies - SVG
 *
 * @package    Plaiceholder
 * @subpackage Svg
 */

namespace accudio\Plaiceholder;

class SVG {
  /**
   * generate
   *
   * @param  Imagick $image
   * @param  mixed   $inline_styles
   *
   * @return string  SVG markup ready to be embedded into a HTML document
   */
  public static function generate($image, $inline_styles = true)
  {
    $rows = Util::get_pixel_array($image);

    $rects = [];

    foreach ($rows as $row => $pixels) {
      // loops through each pixel
      foreach ($pixels as $col => $pixel) {
        // if there is an alpha channel use that, otherwise use 1
        $opacity = ($pixel['a'] ?? false) ? $pixel['a'] : 1;

        $rects[] = sprintf(
          '<rect fill="%s" fill-opacity="%s" x="%s" y="%s" width="1" height="1"/>',
          self::rgb($pixel),
          $opacity,
          $col,
          $row
        );
      }
    }

    $template = '<svg xmlns="http://www.w3.org/2000/svg" width="100%%" height="100%%" shapeRendering="crispEdges" preserveAspectRatio="none" viewBox="0 0 %s %s">%s</svg>';
    if ($inline_styles) {
      $template = '<svg xmlns="http://www.w3.org/2000/svg" class="position:absolute;top:50%%;left:50%%;transform-origin:top left;transform:translate(-50%%, -50%%);right:0;bottom:0" width="100%%" height="100%%" shapeRendering="crispEdges" preserveAspectRatio="none" viewBox="0 0 %s %s">%s</svg>';
    }

    return sprintf(
      $template,

      $image->getImageWidth(),
      $image->getImageHeight(),
      implode('', $rects)
    );
  }

  /**
   * rgb
   *
   * Converts colour array from ImageMagick into CSS rgb
   *
   * @param  array $pixel Colour array in format ['r' => 0-255, 'g' => 0-255, 'b' => 0-255]
   *
   * @return string CSS colour string in `rgb()` format
   */
  private static function rgb($pixel)
  {
    return sprintf(
      'rgb(%s,%s,%s)',
      $pixel['r'],
      $pixel['g'],
      $pixel['b']
    );
  }
}
