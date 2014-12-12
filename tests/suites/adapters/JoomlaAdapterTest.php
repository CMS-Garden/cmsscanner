<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\JoomlaAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class JoomlaAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class JoomlaAdapterTest extends \PHPUnit_Framework_TestCase
{
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
            ->name('dummy.php')->contains('#content')
            ->name('configuration.php')->contains('#empty');

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

        $this->assertCount(9, $results);
        $this->assertEquals(5, $falseCount);
        $this->assertArrayHasKey('', $results);
        $this->assertArrayHasKey('1.5.25', $results);
        $this->assertArrayHasKey('1.6.6', $results);
        $this->assertArrayHasKey('1.7.3', $results);
        $this->assertArrayHasKey('2.5.24', $results);
        $this->assertArrayHasKey('3.0.5', $results);
        $this->assertArrayHasKey('3.1.1', $results);
        $this->assertArrayHasKey('3.2.0', $results);
        $this->assertArrayHasKey('3.3.4', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}