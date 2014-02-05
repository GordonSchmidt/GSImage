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

/**
 * Test abstract image driver.
 *
 * @author Gordon Schmidt <schmidt.gordon@web.de>
 */
class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Instance of driver
     *
     * @var GSImage\Driver\DriverInterface
     */
    protected $driver;

    /**
     * Setup for tests - create driver
     */
    public function setUp()
    {
        $this->driver = $this->getMockForAbstractClass('\GSImage\Driver\AbstractDriver');
    }

    /**
     * Test getting storage for image string
     *
     * @param string $image
     * @param string $storage
     * @covers GSImage\Driver\AbstractDriver::getImageStorage
     * @dataProvider provideImageStorage
     */
    public function testGetImageStorage($image, $storage)
    {
        $this->assertSame($storage, $this->driver->getImageStorage($image));
    }

    /**
     * Test getting image format for image string
     *
     * @param string $image
     * @param array  $info
     * @covers GSImage\Driver\AbstractDriver::getImageFormat
     * @dataProvider provideImageInfo
     */
    public function testGetImageFormat($image, $info)
    {
        $this->assertSame($info[2], $this->driver->getImageFormat($image));
    }

    /**
     * Test getting info for image string
     *
     * @param string $image
     * @param array  $info
     * @covers GSImage\Driver\AbstractDriver::getInfo
     * @dataProvider provideImageInfo
     */
    public function testGetInfo($image, $info)
    {
        $this->assertSame($info, $this->driver->getInfo($image));
    }

    /**
     * Test loading image
     *
     * @param string $image
     * @param string $string
     * @covers GSImage\Driver\AbstractDriver::load
     * @dataProvider provideImageString
     */
    public function testLoad($image, $string)
    {
        $this->mockLoadFromString($string);
        $this->assertTrue(is_resource($this->driver->load($image)));
    }

    /**
     * Test loading image to string
     *
     * @param string $image
     * @param string $string
     * @covers GSImage\Driver\AbstractDriver::loadToString
     * @dataProvider provideImageString
     */
    public function testLoadToString($image, $string)
    {
        $this->assertSame($string, $this->driver->loadToString($image));
    }

    /**
     * Test exception when loading image to string
     *
     * @covers GSImage\Driver\AbstractDriver::loadToString
     */
    public function testLoadToStringException()
    {
        $this->setExpectedException('\GSImage\Exception\RuntimeException');
        $this->driver->loadToString('http://blubb.er/narf.png');
    }

    /**
     * Test saving image
     *
     * @param string $image
     * @param string $string
     * @covers GSImage\Driver\AbstractDriver::save
     * @dataProvider provideImageString
     */
    public function testSave($image, $string)
    {
        $resource = @imagecreatefromstring($string);
        $this->mockSaveToString($string);
        $this->assertEquals(
            getimagesizefromstring($string),
            getimagesizefromstring($this->driver->save($resource, array('format' => IMAGETYPE_PNG)))
        );
    }

    /**
     * Test saving image from string to file
     *
     * @covers GSImage\Driver\AbstractDriver::saveFromString
     */
    public function testSaveFromStringToFile()
    {
        $file = sys_get_temp_dir() . '/tmp-test-' . rand();
        $this->assertSame($file, $this->driver->saveFromString('blubb', array('file' => $file)));
        $this->assertFileExists($file);
        $this->assertSame('blubb', file_get_contents($file));
    }

    /**
     * Test saving image from string to string
     *
     * @covers GSImage\Driver\AbstractDriver::saveFromString
     */
    public function testSaveFromStringToString()
    {
        $this->assertSame('blubb', $this->driver->saveFromString('blubb'));
    }

    /**
     * Test exception when saving image from string
     *
     * @covers GSImage\Driver\AbstractDriver::saveFromString
     */
    public function testSaveFromStringException()
    {
        $this->setExpectedException('\GSImage\Exception\RuntimeException');
        $file = '/root/tmp-test-' . rand(); //only works if phpunit not executed as root
        $this->driver->saveFromString('blubb', array('file' => $file));
    }

    /**
     * Test ensure format and storage, when input meets all expectations
     *
     * @param string       $image
     * @param array|int    $formats
     * @param array|string $storages
     * @covers GSImage\Driver\AbstractDriver::ensure
     * @covers GSImage\Driver\AbstractDriver::getFileOptionForSaving
     * @dataProvider provideEnsureSame
     */
    public function testEnsureSame($image, $formats, $storages)
    {
        $this->assertSame($image, $this->driver->ensure($image, $formats, $storages));
    }

    /**
     * Test ensure format and storage, when input storage is different and it's string
     *
     * @param string       $image
     * @param array|int    $formats
     * @param array|string $storages
     * @covers GSImage\Driver\AbstractDriver::ensure
     * @covers GSImage\Driver\AbstractDriver::getFileOptionForSaving
     * @dataProvider provideEnsureSameFormatStringStorage
     */
    public function testEnsureSameFormatStringStorage($image, $formats, $storages)
    {
        $string = file_get_contents($image);
        $this->assertSame($string, $this->driver->ensure($image, $formats, $storages));
    }

    /**
     * Test ensure format and storage, when input storage is different and it's file
     *
     * @param string       $image
     * @param array|int    $formats
     * @param array|string $storages
     * @covers GSImage\Driver\AbstractDriver::ensure
     * @covers GSImage\Driver\AbstractDriver::getFileOptionForSaving
     * @dataProvider provideEnsureSameFormatFileStorage
     */
    public function testEnsureSameFormatFileStorage($image, $formats, $storages)
    {
        $file = $this->driver->ensure($image, $formats, $storages);
        $this->assertFileExists($file);
        $this->assertSame($image, file_get_contents($file));
    }

    /**
     * Test ensure format and storage, when input format is different
     *
     * @param string       $image
     * @param array|int    $formats
     * @param array|string $storages
     * @param string       $string
     * @covers GSImage\Driver\AbstractDriver::ensure
     * @covers GSImage\Driver\AbstractDriver::getFileOptionForSaving
     * @dataProvider provideEnsureDifferent
     */
    public function testEnsureDifferent($image, $formats, $storages, $string)
    {
        $this->mockSaveToString($string);
        $this->assertEquals(
            getimagesizefromstring($string),
            getimagesizefromstring($this->driver->ensure($image, $formats, $storages))
        );
    }

    /**
     * Test exception when ensure format and storage
     *
     * @param string       $image
     * @param array|int    $formats
     * @param array|string $storages
     * @covers GSImage\Driver\AbstractDriver::ensure
     * @covers GSImage\Driver\AbstractDriver::getFileOptionForSaving
     * @dataProvider provideEnsureException
     */
    public function testEnsureException($image, $formats, $storages)
    {
        $this->setExpectedException('\GSImage\Exception\RuntimeException');
        $this->assertSame($image, $this->driver->ensure($image, $formats, $storages));
    }

    /**
     * Provide image strings and matching storages
     *
     * @return array
     */
    public function provideImageStorage()
    {
        return array(
            array('', DriverInterface::IMAGE_STORAGE_FILE),
            array('http://test.org/test.png', DriverInterface::IMAGE_STORAGE_URL),
            array(dirname(dirname(__DIR__)) . '/assets/test.png', DriverInterface::IMAGE_STORAGE_FILE),
            array('blubb', DriverInterface::IMAGE_STORAGE_STRING),
        );
    }

    /**
     * Provide image strings and infos
     *
     * @return array
     */
    public function provideImageInfo()
    {
        $basePath = dirname(dirname(__DIR__));
        return array(
            array(
                $basePath . '/assets/test.gif',
                array(0 => 30, 1 => 5, 2 => 1, 3 => 'width="30" height="5"', 'bits' => 1, 'channels' => 3, 'mime' => 'image/gif')
            ),
            array(
                $basePath . '/assets/test.jpg',
                array(0 => 30, 1 => 5, 2 => 2, 3 => 'width="30" height="5"', 'bits' => 8, 'channels' => 3, 'mime' => 'image/jpeg')
            ),
            array(
                $basePath . '/assets/test.png',
                array(0 => 30, 1 => 5, 2 => 3, 3 => 'width="30" height="5"', 'bits' => 8, 'mime' => 'image/png')
            ),
            array(
                file_get_contents($basePath . '/assets/test.png'),
                array(0 => 30, 1 => 5, 2 => 3, 3 => 'width="30" height="5"', 'bits' => 8, 'mime' => 'image/png')
            ),
        );
    }

    /**
     * Provide image strings and image content
     *
     * @return array
     */
    public function provideImageString()
    {
         $basePath = dirname(dirname(__DIR__));
         $content = file_get_contents($basePath . '/assets/test.png');
         return array(
             array($basePath . '/assets/test.png', $content),
             array($content, $content),
         );
    }

    /**
     * Provide image strings and matching storages and formats
     *
     * @return array
     */
    public function provideEnsureSame()
    {
        $file = dirname(dirname(__DIR__)) . '/assets/test.png';
        $image = file_get_contents($file);
        $formats = array(IMAGETYPE_GIF, IMAGETYPE_PNG);
        return array(
            array($file, IMAGETYPE_PNG, DriverInterface::IMAGE_STORAGE_FILE),
            array($file, $formats, array(DriverInterface::IMAGE_STORAGE_URL, DriverInterface::IMAGE_STORAGE_FILE)),
            array($image, $formats, array(DriverInterface::IMAGE_STORAGE_FILE, DriverInterface::IMAGE_STORAGE_STRING)),
        );
    }

    /**
     * Provide image strings and matching format but string storage
     *
     * @return array
     */
    public function provideEnsureSameFormatStringStorage()
    {
        $file = dirname(dirname(__DIR__)) . '/assets/test.png';
        $formats = array(IMAGETYPE_GIF, IMAGETYPE_PNG);
        return array(
            array($file, IMAGETYPE_PNG, DriverInterface::IMAGE_STORAGE_STRING),
            array($file, $formats, array(DriverInterface::IMAGE_STORAGE_URL, DriverInterface::IMAGE_STORAGE_STRING)),
        );
    }

    /**
     * Provide image strings and matching format but file storage
     *
     * @return array
     */
    public function provideEnsureSameFormatFileStorage()
    {
        $image = file_get_contents(dirname(dirname(__DIR__)) . '/assets/test.png');
        $formats = array(IMAGETYPE_GIF, IMAGETYPE_PNG);
        return array(
            array($image, IMAGETYPE_PNG, DriverInterface::IMAGE_STORAGE_FILE),
            array($image, $formats, array(DriverInterface::IMAGE_STORAGE_URL, DriverInterface::IMAGE_STORAGE_FILE)),
        );
    }

    /**
     * Provide image strings and different format
     *
     * @return array
     */
    public function provideEnsureDifferent()
    {
        $file = dirname(dirname(__DIR__)) . '/assets/test.png';
        $image = file_get_contents($file);
        $gif = file_get_contents(dirname(dirname(__DIR__)) . '/assets/test.gif');
        $jpg = file_get_contents(dirname(dirname(__DIR__)) . '/assets/test.jpg');
        $formats = array(IMAGETYPE_GIF, IMAGETYPE_JPEG);
        return array(
            array($file, IMAGETYPE_GIF, DriverInterface::IMAGE_STORAGE_STRING, $gif),
            array($file, IMAGETYPE_JPEG, DriverInterface::IMAGE_STORAGE_STRING, $jpg),
            array($image, $formats, array(DriverInterface::IMAGE_STORAGE_FILE, DriverInterface::IMAGE_STORAGE_STRING), $gif),
        );
    }

    /**
     * Provide image strings and formats and invalid storage
     *
     * @return array
     */
    public function provideEnsureException()
    {
        $file = dirname(dirname(__DIR__)) . '/assets/test.png';
        $image = file_get_contents($file);
        $formats = array(IMAGETYPE_GIF, IMAGETYPE_JPEG);
        return array(
            array($file, IMAGETYPE_GIF, DriverInterface::IMAGE_STORAGE_URL),
            array($image, IMAGETYPE_GIF, DriverInterface::IMAGE_STORAGE_URL),
            array($file, $formats, DriverInterface::IMAGE_STORAGE_URL),
        );
    }

    /**
     * Mock abstract method saveToString - overwrite with empty method in test classes of real drivers
     *
     * @param string $string
     */
    protected function mockSaveToString($string)
    {
        $this->driver
            ->expects($this->once())
            ->method('saveToString')
            ->will($this->returnValue($string));
    }

    /**
     * Mock abstract method loadFromString - overwrite with empty method in test classes of real drivers
     *
     * @param string $string
     */
    protected function mockLoadFromString($string)
    {
        $resource = @imagecreatefromstring($string);
        $this->driver
            ->expects($this->once())
            ->method('loadFromString')
            ->will($this->returnValue($resource));
    }
}
