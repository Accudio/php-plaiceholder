<?php
/**
 * Plaiceholder for PHP - Strategies - CSS
 *
 * @package    PHPPlaiceholder
 * @subpackage CSS
 */

namespace accudio\PHPPlaiceholder;

class CSS {
  /**
   * generate
   *
   * @param  Imagick $image
   *
   * @return mixed Background properties including `background-image`, `background-position`, `background-size`, and `background-repeat`, outputted as a style attribute, associative array or custom properties.
   */
  public static function generate($image, $output = 'style')
  {
    $rows = Util::get_pixel_array($image);

    $linearGradients = [];
    foreach ($rows as $row) {
      $pixels = array_map(function($pixel) {
        return self::rgb($pixel);
      }, $row);

      $gradients = [];
      foreach ($pixels as $i => $pixel) {
        $start = '';
        if ($i !== 0) {
          $start = round(($i / count($pixels)) * 100, 2) . '%';
        }

        $end = '';
        if ($i !== count($pixels)) {
          $end = round((($i + 1) / count($pixels)) * 100, 2) . '%';
        }

        $gradients[] = "$pixel $start $end";
      }

      $gradients = implode(',', $gradients);
      $linearGradients[] = "linear-gradient(90deg, $gradients)";
    }

    $backgroundImage = implode(',', $linearGradients);

    $backgroundPositions = [];
    foreach ($linearGradients as $i => $gradient) {
      if ($i === 0) {
        $backgroundPositions[] = '0 0';
      } else {
        $backgroundPositions[] = '0 ' . round(($i / (count($linearGradients) - 1)) * 100, 2) . '%';
      }
    }
    $backgroundPosition = implode(',', $backgroundPositions);

    $backgroundSize = '100% ' . round(100 / count($linearGradients), 2) . '%';

    $properties = [
      'background-image'     => implode(',', $linearGradients),
      'background-position'  => $backgroundPosition,
      'background-size'      => $backgroundSize,
      'background-repeat'    => 'no-repeat'
    ];

    switch ($output) {
      case 'style':
        return sprintf(
          'background-image:%s;background-position:%s;background-size:%s;background-repeat:no-repeat;',
          $backgroundImage,
          $backgroundPosition,
          $backgroundSize
        );

      case 'properties':
        return sprintf(
          '--plaice-image:%s;--plaice-position:%s;--plaice-size:%s;--plaice-repeat:no-repeat;',
          $backgroundImage,
          $backgroundPosition,
          $backgroundSize
        );

      case 'array':
        return [
          'background-image'     => $backgroundImage,
          'background-position'  => $backgroundPosition,
          'background-size'      => $backgroundSize,
          'background-repeat'    => 'no-repeat'
        ];
    }
  }

  /**
   * rgb
   *
   * Converts colour array from ImageMagick into CSS rgb/rgba
   *
   * @param  array $pixel Colour array in format ['r' => 0-255, 'g' => 0-255, 'b' => 0-255, 'a' => 0-1]
   *
   * @return string CSS colour string in `rgb()` or `rgba()` format
   */
  private static function rgb($pixel)
  {
    // if there is an alpha channel output rgba
    if ($pixel['a'] ?? false) {
      return sprintf(
        'rgba(%s,%s,%s,%s)',
        $pixel['r'],
        $pixel['g'],
        $pixel['b'],
        $pixel['a']
      );
    }

    return sprintf(
      'rgb(%s,%s,%s)',
      $pixel['r'],
      $pixel['g'],
      $pixel['b']
    );
  }
}
