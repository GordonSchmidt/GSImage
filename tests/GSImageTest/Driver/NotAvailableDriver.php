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

namespace GSImageTest\Driver;

use GSImage\Driver\DriverInterface;
use GSImage\Exception;

/**
 * Not available dummy driver for tests.
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
class NotAvailableDriver implements DriverInterface
{
    /**
     * Construct driver - check if available
     */
    public function __construct()
    {
        throw new Exception\RuntimeException('this driver is never available - please use another image driver');
    }

    /**
     * Get width of image resource
     *
     * @param resource $image
     * @return int
     */
    public function getWidth($image)
    {
        return 0;
    }

    /**
     * Get height of image resource
     *
     * @param resource $image
     * @return int
     */
    public function getHeight($image)
    {
        return 0;
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
        return 'blubb';
    }

    /**
     * Get storage type of image
     *
     * @param string $image
     * @return string
     */
    public function getImageStorage($image)
    {
        return 'blubb';
    }

    /**
     * Get format of image
     *
     * @param string $image
     * @return string
     */
    public function getImageFormat($image)
    {
        return 'png';
    }

    /**
     * Get info of image
     *
     * @param string $image
     * @return array
     */
    public function getInfo($image)
    {
        return array();
    }

    /**
     * Ensure certain image storages und formats
     *
     * @param string       $image
     * @param array|string $formats
     * @param array|string $storages
     * @return string
     */
    public function ensure($image, $formats, $storages)
    {
        return $image;
    }

    /**
     * Load image
     *
     * @param string $image
     * @param resource
     */
    public function load($image)
    {
        return 'blubb';
    }

    /**
     * Save image
     *
     * @param resource $image
     * @param array    $options
     * @param string
     */
    public function save($image, $options)
    {
        return 'blubb';
    }

    /**
     * Load image to string
     *
     * @param string $image
     * @param string
     */
    public function loadToString($image)
    {
        return 'blubb';
    }

    /**
     * Save image from string
     *
     * @param string $image
     * @param array  $options
     * @param string
     */
    public function saveFromString($image, $options)
    {
        return 'blubb';
    }
}
