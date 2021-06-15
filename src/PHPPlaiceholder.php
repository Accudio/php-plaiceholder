<?php
/**
 * Plaiceholder for PHP
 *
 * PHP implementation of Plaiceholder
 *
 * @see     https://github.com/joe-bell/plaiceholder Original JavaScript implementation of Plaiceholder
 *
 * @author  Alistair Shepherd <alistair@accudio.com>
 * @author  Joe Bell <joe@joebell.co.uk>
 * @license Apache-2.0
 * @version 1.1.0
 * @package PHPPlaiceholder
 */

namespace accudio\PHPPlaiceholder;

/**
 * PHPPlaiceholder
 *
 * Root class for PHPPlaiceholder, includes image loading, optimisation and methods for calling the different strategies.
 */
class PHPPlaiceholder {
  const SIZE_MIN = 4;
  const SIZE_MAX = 64;

  // variables
  private string $path;
  private array $options;
  private $src;
  private $image;
  private $css;
  private $svg;
  private $base64;
  private $blurhash;

  // =================================================
  // INITIALISATION AND CREATION OF IMAGE
  // =================================================

  /**
   * __construct
   *
   * Sets initial properties and kicks off loading and optimising image for later use.
   *
   * @param  string $path     Absolute path to source image
   * @param  array  $options  Associative array of options. Only option available is `size`, whcih configures the chunk size of generated plaiceholders. `size` should be a value between 4-64, defaults to 4.
   *
   * @return PHPPlaiceholder     Instance of Plaiceholder
   */
  function __construct($path, $options = [])
  {
    $this->path = $path;
    $this->options = $options;
    $this->load_image();
    $this->optimise_image();
  }

  /**
   * load_image
   *
   * Creates Imagick instance with image path, sets to $src property.
   */
  private function load_image()
  {
    /**
     * Remote Images
     *
     * If the provided path is remote, download the image and load it into Imagick as an image blob
     */
    if (substr($this->path, 0, 4) === 'http') {
      // using a stream context to specify a low timeout
      $stream_context = stream_context_create([
        'http' => [
          'method'  => 'GET',
          'timeout' => 5
        ]
      ]);
      $remote_img = file_get_contents($this->path, false, $stream_context);
      if (!$remote_img) return false;

      $image = new \Imagick();
      $success = $image->readImageBlob($remote_img);
      if (!$success) return false;

      return $this->src = $image;
    }

    /**
     * Local Images
     *
     * Check image exists before loading into Imagick
     */
    if (!file_exists($this->path)) return false;
    $this->src = new \Imagick($this->path);
  }

  /**
   * optimise_image
   *
   * If present constrains user-defined size within constant max and min sizes, otherwise falls back to min.
   * Makes a clone of the src image and resizes it to specified square size. THis maintains aspect ratio and fits within configured dimensions.
   * Assigns to property $image for later use by strategies.
   */
  private function optimise_image()
  {
    // get desired size if present, and constrain between SIZE_MIN and SIZE_MAX
    $desired_size = $this->options['size'] ?? self::SIZE_MIN;
    $size = max([min([$desired_size, self::SIZE_MAX]), self::SIZE_MIN]);

    $this->image = clone $this->src;
    $this->image->thumbnailImage($size, $size, true);
  }

  // =================================================
  // STRATEGIES
  // =================================================
  /**
   * get_css
   *
   * Converts image into a low-res placeholder, outputted as a set of linear-gradients.
   * For a "blurred" effect, extend the returned styles with filter: blur(<value>) and transform: scale(<value>).
   *
   * @return mixed Background properties including `background-image`, `background-position`, `background-size`, and `background-repeat`, outputted as a style attribute, associative array or custom properties.
   */
  public function get_css($output = 'style')
  {
    // if already run, return cached output
    if ($this->css) return $this->css;

    // generate css output
    $image = clone $this->image;
    $this->css = CSS::generate($image, $output);
    return $this->css;
  }

  /**
   * get_svg
   *
   * Converts image into a low-res placeholder, outputted as an SVG with a series of <rect> elements.
   * For a "blurred" effect, extend the returned styles with filter: blur(<value>) and transform: scale(<value>).
   *
   * @param  boolean (optional) Should inline styles be added to svg element? Defaults to true.
   *
   * @return string  SVG markup ready to be embedded into a HTML document
   */
  public function get_svg($inline_styles = true)
  {
    // if already run, return cached output
    if ($this->svg) return $this->svg;

    // generate svg output
    $image = clone $this->image;
    $this->svg = SVG::generate($image, $inline_styles);
    return $this->svg;
  }

  /**
   * get_base64
   *
   * Converts image into a low-res image, encoded as Base64 string ready to be used in `src` attribute etc.
   *
   * @return string Base64 string, including data and format prefix
   */
  public function get_base64()
  {
    // if already run, return cached output
    if ($this->base64) return $this->base64;

    // generate base64 output
    $image = clone $this->image;
    $this->base64 = Base64::generate($image);
    return $this->base64;
  }

  /**
   * get_blurhash
   *
   * Converts image into a low-res image, encoded as Blurhash string accompanied by it's width and height.
   * This can be passed into a blurhash JavaScript library.
   *
   * @see https://blurha.sh/
   *
   * @return string Blurhash string
   */
  public function get_blurhash()
  {
    // if already run, return cached output
    if ($this->blurhash) return $this->blurhash;

    // generate base64 output
    $image = clone $this->image;
    $this->blurhash = Blurhash::generate($image);
    return $this->blurhash;
  }
}
