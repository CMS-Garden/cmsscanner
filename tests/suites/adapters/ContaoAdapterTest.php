<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
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
        $finder = Finder::create()
            ->files()
            ->in(CMSSCANNER_MOCKFILES_PATH)
            ->name('dummy.php')
            ->name('configuration.php')
        ;

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
        $this->assertEquals(19, $falseCount);
        $this->assertArrayHasKey('2.10.4', $results);
        $this->assertArrayHasKey('3.1.0', $results);
        $this->assertArrayHasKey('4.1.1', $results);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\System', current($results));
    }

    /**
     * Tests that Contao 2 modules are correctly detected
     */
    public function testContao2Modules()
    {
        $modules = $this->object->detectModules(
            new \SplFileInfo(CMSSCANNER_MOCKFILES_PATH . '/contao/contao2')
        );

        $this->assertCount(1, $modules);
        $this->assertInstanceOf('Cmsgarden\Cmsscanner\Detector\Module', $modules[0]);
        $this->assertEquals('z_custom', $modules[0]->name);
        $this->assertEquals(CMSSCANNER_MOCKFILES_PATH . '/contao/contao2/system/modules/z_custom', $modules[0]->path);
    }

    /**
     * Tests that Contao 3 modules are correctly detected
     */
    public function testContao3Modules()
    {
        $expected = array(
            array(
                'name'    => 'z_custom',
                'path'    => '/contao/contao3/system/modules/z_custom',
                'version' => '',
                'type'    => '',
            ),
            array(
                'name'    => 'folderpage',
                'path'    => '/contao/contao3/system/modules/folderpage',
                'version' => '1.2.4',
                'type'    => '',
            ),
            array(
                'name'    => 'dcawizard',
                'path'    => '/contao/contao3/system/modules/dcawizard',
                'version' => '1.0.0',
                'type'    => '',
            ),
            array(
                'name'    => 'terminal42/notification_center',
                'path'    => '/contao/contao3/composer/vendor/terminal42/notification_center',
                'version' => '1.3.2',
                'type'    => '',
            ),
        );

        $actual = $this->object->detectModules(
            new \SplFileInfo(CMSSCANNER_MOCKFILES_PATH . '/contao/contao3')
        );

        $actual = json_decode(json_encode($actual), true);

        foreach ($actual as $i => $value) {
            $actual[$i] = str_replace(dirname(dirname(__DIR__)) . '/mockfiles', '', $value);
        }

        $this->assertEquals(
            $actual,
            $expected,
            $message = '',
            $delta = 0.0,
            $maxDepth = 10,
            $canonicalize = true,
            $ignoreCase = false
        );
    }
}
