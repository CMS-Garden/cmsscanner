<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Adapters;

use Cmsgarden\Cmsscanner\Detector\Adapter\ContenidoAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Class ContenidoAdapterTest
 * @package Cmsgarden\Cmsscanner\Tests\Adapters
 *
 * @since   1.0.0
 */
class ContenidoAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContenidoAdapter */
    public $object;

    public function setUp()
    {
        $this->object = new ContenidoAdapter();
    }

    public function testCorrectNameIsReturned()
    {
        $this->assertEquals('Contenido', $this->object->getName());
    }

    public function testSystemsAreDetected()
    {
        $finder = new Finder();
        $finder->files()->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php')
            ->name('contenido.css')
            ->name('template.info.html')
            ->name('template.str_overview.html')
            ->name('template.lang_left_top.html')
            ->name('navigation.xml')
            ->name('template.newsletter_left_top.html')
            ->name('subnavi.js')
            ->name('rowMark.js')
            ->name('template.default_subnav.html')
            ->name('navigation.xml');

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

        $this->assertCount(17, $results);
        $this->assertEquals(1, $falseCount);
        $this->assertArrayHasKey('', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.8.14 or CONTENIDO Version 4.8.15', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.8.16', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.8.17', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.8.18', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.8.19', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.0 Alpha 3', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.0 Alpha 1', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.0 RC 1', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.0', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.1', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.2', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.3', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.4', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.5', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.6', $results);
        $this->assertArrayHasKey('CONTENIDO Version 4.9.8 or CONTENIDO Version 4.9.7', $results);

        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }
}
