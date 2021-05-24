<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\JoomlaAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class JoomlaAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class JoomlaAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var JoomlaAdapter */
    public $object;

    public function setUp()
    {
        $this->object = new JoomlaAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Joomla', $this->object->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php')
            ->name('configuration.php');

        $finder = $this->object->appendDetectionCriteria($finder);

        $results = array();
        $falseCount = 0;

        foreach ($finder as $file) {
            $system = $this->object->detectSystem($file);

            if ($system == false) {
                $falseCount++;
                continue;
            }

            $system->version = $this->object->detectVersion($system->getPath());

            // Append successful result to array
            $results[$system->version] = $system;
        }

        $this->assertCount(14, $results);
        $this->assertEquals(5, $falseCount);
        $this->assertArrayHasKey('', $results);
        $this->assertArrayHasKey('1.0.11', $results);
        $this->assertArrayHasKey('1.5.25', $results);
        $this->assertArrayHasKey('1.6.6', $results);
        $this->assertArrayHasKey('1.7.3', $results);
        $this->assertArrayHasKey('2.5.24', $results);
        $this->assertArrayHasKey('3.0.5', $results);
        $this->assertArrayHasKey('3.1.1', $results);
        $this->assertArrayHasKey('3.2.0', $results);
        $this->assertArrayHasKey('3.3.4', $results);
        $this->assertArrayHasKey('3.4.8', $results);
        $this->assertArrayHasKey('3.5.1', $results);
        $this->assertArrayHasKey('3.8.0', $results);
        $this->assertArrayHasKey('4.0.0', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }

    public function testModulesAreDetected()
    {
        $paths = array(
            "/joomla/joomla1.5",
            "/joomla/joomla2.5",
            "/joomla/joomla3.5",
        );

        foreach ($paths as $path) {
            $path = new \SplFileInfo(CMSSCANNER_MOCKFILES_PATH . $path);

            $modules = $this->object->detectModules($path);

            $this->assertCount(4, $modules);
            $this->assertEquals('UnitTests', $modules[0]->name);
            $this->assertEquals('module', $modules[0]->type);
            $this->assertEquals('1.0.0', $modules[0]->version);
            $this->assertEquals('Unittest', $modules[1]->name);
            $this->assertEquals('component', $modules[1]->type);
            $this->assertEquals('1.0.0', $modules[1]->version);
            $this->assertEquals('Content - Unittest', $modules[2]->name);
            $this->assertEquals('plugin', $modules[2]->type);
            $this->assertEquals('1.0.0', $modules[2]->version);
            $this->assertEquals('unitplate', $modules[3]->name);
            $this->assertEquals('template', $modules[3]->type);
            $this->assertEquals('1.0.0', $modules[3]->version);
        }
    }
}
