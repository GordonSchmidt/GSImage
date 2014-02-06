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

namespace GSImageTest\Service;

use GSImage\Entity\Image;

/**
 * Test image model.
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Instance of image
     *
     * @var GSImage\Entity\Image
     */
    protected $image;

    /**
     * Instance of driver
     *
     * @var GSImage\Driver\DriverInterface
     */
    protected $driver;

    /**
     * Setup for tests - create image with dummy driver
     */
    public function setUp()
    {
        $this->driver = $this->getMock('\GSImage\Driver\DriverInterface');
        $this->driver
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue('blah'));
        $this->image = new Image('blubb', $this->driver);
    }

    /**
     * Test successful creation of image
     *
     * @covers \GSImage\Entity\Image::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('blah', 'resource', $this->image);
    }

    /**
     * Test driver call to getWidth
     *
     * @covers \GSImage\Entity\Image::getWidth
     */
    public function testGetWidth()
    {
        $this->driver
            ->expects($this->once())
            ->method('getWidth')
            ->with($this->equalTo('blah'))
            ->will($this->returnValue('narf'));
        $this->assertSame('narf', $this->image->getWidth());
    }

    /**
     * Test driver call to getHeight
     *
     * @covers \GSImage\Entity\Image::getHeight
     */
    public function testGetHeight()
    {
        $this->driver
            ->expects($this->once())
            ->method('getHeight')
            ->with($this->equalTo('blah'))
            ->will($this->returnValue('narf'));
        $this->assertSame('narf', $this->image->getHeight());
    }

    /**
     * Test driver call to getPixel
     *
     * @covers \GSImage\Entity\Image::getPixel
     */
    public function testGetPixel()
    {
        $x = rand();
        $y = rand();
        $this->driver
            ->expects($this->once())
            ->method('getPixel')
            ->with($this->equalTo('blah', $x, $y))
            ->will($this->returnValue('narf'));
        $this->assertSame('narf', $this->image->getPixel($x, $y));
    }

    /**
     * Test successful setter and getter of driver
     *
     * @covers \GSImage\Entity\Image::getDriver
     * @covers \GSImage\Entity\Image::setDriver
     */
    public function testGetSetDriver()
    {
        $basePath = dirname(dirname(dirname(__DIR__)));
        //test autodetection in getDriver
        $image = new Image($basePath . '/tests/assets/test.png');
        $this->assertInstanceOf('\GSImage\Driver\DriverInterface', $image->getDriver());

        //test setter and getter
        $image->setDriver($this->driver);
        $this->assertSame($this->driver, $image->getDriver());
    }

    /**
     * Test successful autodetection of driver
     *
     * @param array  $drivers
     * @param mixed  $result
     * @param string $assert
     * @covers \GSImage\Entity\Image::autodetectDriver
     * @dataProvider provideKnownDrivers
     */
    public function testAutodetectDriver($drivers, $result, $assert)
    {
        $method = new \ReflectionMethod('\GSImage\Entity\Image', 'autodetectDriver');
        $method->setAccessible(true);
        $this->$assert($result, $method->invoke($this->image, $drivers));
    }

    /**
     * Provide known drivers to test autodetectDriver method
     *
     * @return array
     */
    public function provideKnownDrivers()
    {
        return array(
            array(
                array(
                    '\GSImageTest\Driver\InvalidDriver',
                    '\GSImageTest\Driver\NotAvailableDriver',
                    '\GSImageTest\Driver\ValidDriver'
                ),
                '\GSImageTest\Driver\ValidDriver',
                'assertInstanceOf'
            ),
            array(
                array('\GSImageTest\Driver\InvalidDriver', '\GSImageTest\Driver\NotAvailableDriver'),
                null,
                'assertSame'
            ),
        );
    }
}
