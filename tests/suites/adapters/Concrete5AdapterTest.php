<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\Concrete5Adapter;
use Symfony\Component\Finder\Finder;

/**
 * Class Concrete5AdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class Concrete5AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Concrete5Adapter
     */
    public $object;

    public function setUp()
    {
        $this->object = new Concrete5Adapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Concrete5', $this->object->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php')
            ->name('version.php');
            ->name('concrete.php');

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
        $this->assertEquals(11, $falseCount);
        $this->assertArrayHasKey('', $results);
        $this->assertArrayHasKey('8.1.0', $results);
        $this->assertArrayHasKey('5.6.3.4', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}
