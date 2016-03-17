<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2016 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\ContaoAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class ContaoAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 *
 * @author Andreas Schempp <https://github.com/aschempp>
 */
class ContaoAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContaoAdapter */
    public $object;

    public function setUp()
    {
        $this->object = new ContaoAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Contao', $this->object->getName());
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

        $this->assertCount(3, $results);
        $this->assertEquals(15, $falseCount);
        $this->assertArrayHasKey('2.10.4', $results);
        $this->assertArrayHasKey('3.1.0', $results);
        $this->assertArrayHasKey('4.1.1', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}
