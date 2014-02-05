<?php
/**
 * This file is part of GSImage.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2014 Gordon Schmidt
 * @license   MIT
 */

namespace GSImage\Driver;

use GSImage\Exception;

/**
 * Image driver for gd.
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
class GdDriver extends AbstractDriver
{
    /**
     * Constructor - check for gd
     */
    public function __construct()
    {
        if (!function_exists("gd_info")) {
            throw new Exception\RuntimeException('gd extension is not available - please use another image driver');
        }
    }

    /**
     * Get width of image resource
     *
     * @param resource $image
     * @return int
     */
    public function getWidth($image)
    {
        return imagesx($image);
    }

    /**
     * Get height of image resource
     *
     * @param resource $image
     * @return int
     */
    public function getHeight($image)
    {
        return imagesy($image);
    }

    /**
     * Get pixel value from image resource
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @return string
     */
    public function getPixel($image, $x, $y)
    {
        return imagecolorsforindex($image, imagecolorat($image, $x, $y));
    }

    /**
     * Load image from string
     *
     * @param string $image
     * @param resource
     */
    public function loadFromString($image)
    {
        $resource = @imagecreatefromstring($image);

        if (!is_resource($resource)) {
            throw new Exception\RuntimeException('image could not be loaded');
        }

        return $resource;
    }

    /**
     * Save image to string
     *
     * @param resource $image
     * @param array    $options
     * @param string
     */
    public function saveToString($image, $options)
    {
        $outputMethod = $this->getOutputMethod($options);
        ob_start();
        $outputMethod($image);
        $string = ob_get_contents();
        ob_end_clean();
        return $string;
    }

    /**
     * Get output method for given format
     *
     * @param array $options
     * @return string
     */
    protected function getOutputMethod($options)
    {
        $outputMethod = 'imagegd';
        if (isset($options['format'])) {
            $outputMethod = 'image' . image_type_to_extension($options['format'], false);
        }
        return $outputMethod;
    }
}
