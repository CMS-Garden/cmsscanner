<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\ShopwareAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class PrestashopAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class ShopwareAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var PrestashopAdapter */
    public $object;

    public function setUp()
    {
        $this->object = new ShopwareAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Shopware', $this->object->getName());
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

        $this->assertCount(2, $results);
        $this->assertEquals(1, $falseCount);
        $this->assertArrayHasKey('4.3.2', $results);
        $this->assertArrayHasKey('5.5.1', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}
