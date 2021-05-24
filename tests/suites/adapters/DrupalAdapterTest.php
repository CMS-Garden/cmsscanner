<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\DrupalAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class DrupalAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class DrupalAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DrupalAdapter
     */
    public $object;

    public function setUp()
    {
        $this->object = new DrupalAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Drupal', $this->object->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php');

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
        $this->assertCount(4, $results);
        $this->assertEquals(1, $falseCount);
        $this->assertArrayHasKey('', $results);
        $this->assertArrayHasKey('6.34', $results);
        $this->assertArrayHasKey('7.33', $results);
        $this->assertArrayHasKey('8.0.0-beta3', $results);

        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }

    public function testModulesAreDetected()
    {
        $paths = array(
            6 => '/drupal/drupal6',
            7 => '/drupal/drupal7',
            8 => '/drupal/drupal8',
        );

        foreach ($paths as $version => $path) {
            $path = new \SplFileInfo(CMSSCANNER_MOCKFILES_PATH.$path);

            $modules = $this->object->detectModules($path);
            if ($version === 6) {
                $this->assertCount(0, $modules);
            } elseif ($version === 7) {
                $this->assertCount(7, $modules);
                $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\Module', $modules[0]);

                $expected = array(
                    'name' => 'devel',
                    'path' => $path->getRealPath().'/modules/devel',
                    'version' => '7.x-1.3',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[0]);

                $expected = array(
                    'name' => 'adaptivetheme',
                    'path' => $path->getRealPath().'/sites/all/themes/adaptivetheme/at_admin',
                    'version' => '7.x-3.2',
                    'type' => 'theme',
                );
                $this->assertEquals($expected, (array)$modules[1]);

                $expected = array(
                    'name' => 'date',
                    'path' => $path->getRealPath().'/sites/all/modules/date/date_migrate/date_migrate_example',
                    'version' => '7.x-2.9',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[2]);

                $expected = array(
                    'name' => 'date',
                    'path' => $path->getRealPath().'/sites/all/modules/date',
                    'version' => '7.x-2.9',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[3]);

                $expected = array(
                    'name' => 'devel',
                    'path' => $path->getRealPath().'/sites/all/modules/devel',
                    'version' => '7.x-1.4',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[4]);

                $expected = array(
                    'name' => 'views_bulk_operations',
                    'path' => $path->getRealPath().'/sites/mysite/modules/contrib/views_bulk_operations',
                    'version' => '7.x-3.3',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[5]);

                $expected = array(
                    'name' => 'devel',
                    'path' => $path->getRealPath().'/sites/mysite/modules/contrib/devel',
                    'version' => '7.x-1.5',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[6]);
            } elseif ($version === 8) {
                $this->assertCount(1, $modules);
                $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\Module', $modules[0]);
                $expected = array(
                    'name' => 'ctools',
                    'path' => $path->getRealPath().'/modules/ctools',
                    'version' => '8.x-3.0-alpha23',
                    'type' => 'module',
                );
                $this->assertEquals($expected, (array)$modules[0]);
            }
        }
    }
}
