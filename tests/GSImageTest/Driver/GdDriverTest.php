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

use GSImage\Driver\GdDriver;

/**
 * Test gd image driver (also runs tests of AbstractDriverTest with the gd driver).
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
class GdDriverTest extends AbstractDriverTest
{
    /**
     * Setup for tests - create image with dummy driver
     */
    public function setUp()
    {
        $this->driver = new GdDriver();
    }

    /**
     * Test getting image width from resource
     *
     * @param string $image
     * @param array  $info
     * @covers GSImage\Driver\GdDriver::getWidth
     * @covers GSImage\Driver\GdDriver::getHeight
     * @dataProvider provideImageInfo
     */
    public function testGetWidthAndHeight($image, $info)
    {
        $resource = $this->driver->load($image);
        $this->assertSame($info[0], $this->driver->getWidth($resource));
        $this->assertSame($info[1], $this->driver->getHeight($resource));
    }

    /**
     * Test getting image pixel from resource
     *
     * @param resource $image
     * @param int      $x
     * @param int      $y
     * @param array    $pixel
     * @covers GSImage\Driver\GdDriver::getPixel
     * @dataProvider provideImagePixel
     */
    public function testGetPixel($image, $x, $y, $pixel)
    {
        $this->assertSame($pixel, $this->driver->getPixel($image, $x, $y));
    }

    /**
     * Test loading image from string
     *
     * @param string $image
     * @param string $string
     * @covers GSImage\Driver\GdDriver::loadFromString
     * @dataProvider provideImageString
     */
    public function testLoadFromString($resource, $string)
    {
        $this->assertTrue(is_resource($this->driver->load($string)));
    }

    /**
     * Test exception when loading image from string
     *
     * @covers GSImage\Driver\GdDriver::loadFromString
     */
    public function testLoadFromStringException()
    {
        $this->setExpectedException('\GSImage\Exception\RuntimeException');
        $this->driver->loadFromString('blubb');
    }

    /**
     * Test saving image to string
     *
     * @param string $image
     * @param string $string
     * @covers GSImage\Driver\GdDriver::saveToString
     * @covers GSImage\Driver\GdDriver::getOutputMethod
     * @dataProvider provideImageString
     */
    public function testSaveToString($image, $string)
    {
        $resource = @imagecreatefromstring($string);
        $this->assertEquals(
            $this->getImageInfoFromString($string),
            $this->getImageInfoFromString($this->driver->saveToString($resource, array('format' => IMAGETYPE_PNG)))
        );
    }

    /**
     * Provide image strings and infos
     *
     * @return array
     */
    public function provideImagePixel()
    {
        $resource = imagecreatefrompng(dirname(dirname(__DIR__)) . '/assets/test.png');
        $pixelBlack = array('red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 0);
        $pixelWhite = array('red' => 255, 'green' => 255, 'blue' => 255, 'alpha' => 0);
        return array(
            array($resource, 0, 0, $pixelBlack),
            array($resource, 1, 1, $pixelWhite),
            array($resource, 2, 2, $pixelBlack),
        );
    }

    /**
     * Mock abstract method saveToString - overwrite with empty method in test classes of real drivers
     *
     * @param string $string
     */
    protected function mockSaveToString($string)
    {
    }

    /**
     * Mock abstract method loadFromString - overwrite with empty method in test classes of real drivers
     *
     * @param string $string
     */
    protected function mockLoadFromString($string)
    {
    }
}
