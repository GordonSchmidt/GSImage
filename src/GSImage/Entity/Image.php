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

namespace GSImage\Entity;

use GSImage\Driver\DriverInterface;

/**
 * Model for an image
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
class Image
{
    /**
     * Image resource
     *
     * @var mixed
     */
    protected $resource;

    /**
     * Image driver
     *
     * @var GSImage\Driver\DriverInterface
     */
    protected $driver;

    /**
     * List of known drivers
     *
     * @var GSImage\Driver\DriverInterface[]
     */
    protected $knownDrivers = array(
        'GSImage\Driver\GdDriver',
    );

    public function __construct($data, $driver = null)
    {
        if ($driver instanceof DriverInterface) {
            $this->setDriver($driver);
        }
        $driver = $this->getDriver();
        $this->resource = $driver->load($data);
    }

    /**
     * Get width of image
     *
     * @return int
     */
    public function getWidth()
    {
        $driver = $this->getDriver();
        return $driver->getWidth($this->resource);
    }

    /**
     * Get height of image
     *
     * @return int
     */
    public function getHeight()
    {
        $driver = $this->getDriver();
        return $driver->getHeight($this->resource);
    }

    /**
     * Get pixel value of given coordinate
     *
     * @param int $x
     * @param int $y
     * @return string
     */
    public function getPixel($x, $y)
    {
        $driver = $this->getDriver();
        return $driver->getPixel($this->resource, $x, $y);
    }

    /**
     * Set driver
     *
     * @param GSImage\Driver\DriverInterface $driver
     * @return self
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Get driver
     *
     * @return GSImage\Driver\DriverInterface
     */
    public function getDriver()
    {
        if (null === $this->driver) {
            $this->driver = $this->autodetectDriver($this->knownDrivers);
        }
        return $this->driver;
    }

    /**
     * Auto detect driver
     *
     * @param array $knownDrivers
     * @return GSImage\Driver\DriverInterface|null
     */
    protected function autodetectDriver($knownDrivers)
    {
        foreach ($knownDrivers as $knownDriver) {
            try {
                $driver = new $knownDriver();
                if ($driver instanceof DriverInterface) {
                    return $driver;
                }
            } catch (\Exception $e) {
                //constructor of driver will have to throw an exception, if driver isn't usable
                //    e.g. when needed extensions are not available
                continue;
            }
        }
        return null;
    }
}
