<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2017 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\AlchemyCmsAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class AlchemyCmsAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class AlchemyCmsAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var AlchemyCmsAdapter */
    public $object;

    public function setUp()
    {
        $this->object = new AlchemyCmsAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Alchemy CMS', $this->object->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php')
            ->name('Gemfile.lock');

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

        $this->assertCount(1, $results);
        $this->assertEquals(1, $falseCount);
        $this->assertArrayHasKey('3.5.0', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }

    public function testModulesAreDetected()
    {
        $path = new \SplFileInfo(CMSSCANNER_MOCKFILES_PATH . '/alchemy_cms');

        // IF we implement module detection for this system we need to change the test!
        $modules = $this->object->detectModules($path);

        $this->assertFalse($modules[0]);
    }
}
