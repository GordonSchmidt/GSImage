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
 * Abstract image driver providing basic functionality.
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
abstract class AbstractDriver implements DriverInterface
{
    /**
     * Get storage type of image
     *
     * @param string $image
     * @return string
     */
    public function getImageStorage($image)
    {
        if (@file_exists('file://' . $image)) {
            return DriverInterface::IMAGE_STORAGE_FILE;
        } else if (false === strpos($image, '://')) {
            return DriverInterface::IMAGE_STORAGE_STRING;
        } else {
            return DriverInterface::IMAGE_STORAGE_URL;
        }
    }

    /**
     * Get format of image
     *
     * @param string $image
     * @return int
     */
    public function getImageFormat($image)
    {
        $info = $this->getInfo($image);
        return $info[2];
    }

    /**
     * Get info of image
     *
     * @param string $image
     * @return array
     */
    public function getInfo($image)
    {
        if (DriverInterface::IMAGE_STORAGE_STRING == $this->getImageStorage($image)) {
            $image = 'data://application/octet-stream;base64,'  . base64_encode($image);
        }
        return getimagesize($image);
    }

    /**
     * Ensure certain image storages und formats
     *
     * @param string       $image
     * @param array|int    $formats
     * @param array|string $storages
     * @return string
     */
    public function ensure($image, $formats, $storages)
    {
        $storage = $this->getImageStorage($image);
        $format = $this->getImageFormat($image);

        $storages = is_string($storages) ? array($storages) : $storages;
        $formats = is_int($formats) ? array($formats) : $formats;
        if (in_array($format, $formats)) {
            if (in_array($storage, $storages)) {
                return $image;
            } else {
                $file = $this->getFileOptionForSaving($storages);
                $image = $this->loadToString($image);
                return $this->saveFromString($image, array('file' => $file));
            }
        } else {
            $file = $this->getFileOptionForSaving($storages);
            $resource = $this->load($image);
            return $this->save($resource, array('file' => $file, 'format' => $formats[0]));
        }
    }

    /**
     * Get file option from storages for saving
     *
     * @param array $storages
     * @return string|null
     */
    protected function getFileOptionForSaving($storages)
    {
        if (in_array(DriverInterface::IMAGE_STORAGE_STRING, $storages)) {
            return null;
        } else if (in_array(DriverInterface::IMAGE_STORAGE_FILE, $storages)) {
            return sys_get_temp_dir() . '/tmp-image-' . rand();
        } else {
            throw new Exception\RuntimeException('cannot save to url storage');
        }
    }

    /**
     * Load image
     *
     * @param string $image
     * @return resource
     */
    public function load($image)
    {
        $image = $this->loadToString($image);
        return $this->loadFromString($image);
    }

    /**
     * Save image
     *
     * @param resource $image
     * @param array    $options
     * @return string
     */
    public function save($image, $options)
    {
        $image = $this->saveToString($image, $options);
        return $this->saveFromString($image, $options);
    }

    /**
     * Load image to string
     *
     * @param string $image
     * @return string
     */
    public function loadToString($image)
    {
        $srcStorage = $this->getImageStorage($image);
        if ($srcStorage !== DriverInterface::IMAGE_STORAGE_STRING) {
            $image = @file_get_contents($image);
            if (false === $image) {
                throw new Exception\RuntimeException('could not load image');
            }
        }
        return $image;
    }

    /**
     * Save image from string
     *
     * @param string $image
     * @param array  $options
     * @return string
     */
    public function saveFromString($image, $options = array())
    {
        if (isset($options['file'])) {
           if (false === @file_put_contents($options['file'], $image)) {
                throw new Exception\RuntimeException('could not save image');
           }
           return $options['file'];
        }
        return $image;
    }

    /**
     * Load image string
     *
     * @param string $image
     * @return resource
     */
    abstract public function loadFromString($image);

    /**
     * Save image to string
     *
     * @param resource $image
     * @param array    $options
     * @return string
     */
    abstract public function saveToString($image, $options);
}
