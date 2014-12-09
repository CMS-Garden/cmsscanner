<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\WordpressAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class WordpressAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class WordpressAdapterTest extends \PHPUnit_Framework_TestCase
{
    public $object;

    public function setUp()
    {
        $this->object = new WordpressAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('WordPress', $this->object->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH);

        $finder = $this->object->appendDetectionCriteria($finder);

        $results = array();

        foreach ($finder as $file) {
            $system = $this->object->detectSystem($file);
            $system->version = $this->object->detectVersion($system->getPath());

            // Append successful result to array
            $results[$system->version] = $system;
        }

        $this->assertCount(4, $results);
        $this->assertArrayHasKey('2.2.1', $results);
        $this->assertArrayHasKey('2.9', $results);
        $this->assertArrayHasKey('3.7.5', $results);
        $this->assertArrayHasKey('4.0', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}