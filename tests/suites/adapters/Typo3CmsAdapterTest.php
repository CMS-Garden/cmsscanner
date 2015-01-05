<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\Typo3CmsAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class Typo3CmsAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 * @author Markus Klein <klein.t3@reelworx.at>
 */
class Typo3CmsAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Typo3CmsAdapter
     */
    public $subject;

    public function setUp()
    {
        $this->subject = new Typo3CmsAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('TYPO3 CMS', $this->subject->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php');

        $finder = $this->subject->appendDetectionCriteria($finder);

        $results = array();
        $falseCount = 0;

        foreach ($finder as $file) {
            $system = $this->subject->detectSystem($file);

            if ($system === false) {
                $falseCount++;
                continue;
            }

            $system->version = $this->subject->detectVersion($system->getPath());

            // Append successful result to array
            $results[$system->version] = $system;
        }

        $this->assertCount(3, $results);
        $this->assertSame(1, $falseCount);
        $this->assertArrayHasKey('', $results);
        $this->assertArrayHasKey('6.2.10', $results);
        $this->assertArrayHasKey('4.5.30', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}