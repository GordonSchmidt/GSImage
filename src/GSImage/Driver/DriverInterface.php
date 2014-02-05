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

/**
 * Interface for image driver.
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
interface DriverInterface
{
    const IMAGE_STORAGE_FILE = 'file';
    const IMAGE_STORAGE_URL  = 'url';
    const IMAGE_STORAGE_STRING = 'string';

    /**
     * Get width of image resource
     *
     * @param resource $image
     * @return int
     */
    public function getWidth($image);

    /**
     * Get height of image resource
     *
     * @param resource $image
     * @return int
     */
    public function getHeight($image);

    /**
     * Get pixel value from image resource
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @return string
     */
    public function getPixel($image, $x, $y);

    /**
     * Get storage type of image
     *
     * @param string $image
     * @return string
     */
    public function getImageStorage($image);

    /**
     * Get format of image
     *
     * @param string $image
     * @return string
     */
    public function getImageFormat($image);

    /**
     * Ensure certain image storages und formats
     *
     * @param string       $image
     * @param array|string $formats
     * @param array|string $storages
     * @return string
     */
    public function ensure($image, $formats, $storages);

    /**
     * Load image
     *
     * @param string $image
     * @return resource
     */
    public function load($image);

    /**
     * Save image
     *
     * @param resource $image
     * @param array    $options
     * @return string
     */
    public function save($image, $options);

    /**
     * Load image to string
     *
     * @param string $image
     * @return string
     */
    public function loadToString($image);

    /**
     * Save image from string
     *
     * @param string $image
     * @param array  $options
     * @return string
     */
    public function saveFromString($image, $options);
}
