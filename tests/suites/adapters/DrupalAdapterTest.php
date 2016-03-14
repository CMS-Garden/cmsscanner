<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2016 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
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
}
