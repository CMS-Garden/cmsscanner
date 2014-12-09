<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Detector;

use Cmsgarden\Cmsscanner\Detector\System;

/**
 * Class SystemTest
 * @package Cmsgarden\Cmsscanner\Tests\Detector
 *
 * @since   1.0.0
 */
class SystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function testConstructorAppliesName()
    {
        $object = new System("heregoesthename", new \SplFileInfo(__FILE__));

        $this->assertEquals('heregoesthename', $object->name);
    }

    /**
     * @return void
     */
    public function testConstructorAppliesPath()
    {
        $object = new System("heregoesthename", new \SplFileInfo(__FILE__));

        $this->assertEquals(new \SplFileInfo(__FILE__), $object->path);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     *
     * @return void
     */
    public function testConstructorRequiresRefusesStringAsPath()
    {
        new System("heregoesthename", "/tmp");
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     *
     * @return void
     */
    public function testConstructorRequiresRefusesIntAsPath()
    {
        new System("heregoesthename", 3);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     *
     * @return void
     */
    public function testConstructorRequiresRefusesArrayAsPath()
    {
        new System("heregoesthename", array("/"));
    }

    /**
     * @return void
     */
    public function testGetnameReturnsName()
    {
        $object = new System("somename", new \SplFileInfo(__FILE__));
        $object->name = "heregoesthename";

        $this->assertEquals("heregoesthename", $object->getName());
    }

    /**
     * @return void
     */
    public function testSetnameSetsName()
    {
        $object = new System("somename", new \SplFileInfo(__FILE__));
        $object->setName("heregoesthename");

        $this->assertEquals("heregoesthename", $object->name);
    }

    /**
     * @return void
     */
    public function testGetversionReturnsVersion()
    {
        $object = new System("somename", new \SplFileInfo(__FILE__));
        $object->version = "1.0.0";

        $this->assertEquals("1.0.0", $object->getVersion());
    }

    /**
     * @return void
     */
    public function testSetversionSetsVersion()
    {
        $object = new System("somename", new \SplFileInfo(__FILE__));
        $object->setVersion("1.0.0");

        $this->assertEquals("1.0.0", $object->version);
    }

    /**
     * @return void
     */
    public function testGetpathReturnsPath()
    {
        $object = new System("somename", new \SplFileInfo(__FILE__));

        $path = new \SplFileInfo("tmp.txt");

        $object->path = $path;
        $this->assertEquals($path, $object->getPath());
    }

    /**
     * @return void
     */
    public function testSetpathSetsPath()
    {
        $object = new System("somename", new \SplFileInfo(__FILE__));

        $path = new \SplFileInfo("tmp.txt");

        $object->setPath($path);
        $this->assertEquals($path, $object->path);
    }
}