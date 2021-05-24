<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Command;

use Cmsgarden\Cmsscanner\Tests\Stubs\TestAdapter;

/**
 * Class AbstractDetectionCommandTest
 * @package Cmsgarden\Cmsscanner\Tests\Command
 *
 * @since   1.0.0
 */
class AbstractDetectionCommandTest extends \PHPUnit_Framework_TestCase
{
    public $object;

    /**
     * @return  void
     */

    public function setUp()
    {
        // Create mock of abstract class AbstractDetectionCommand to test concrete methods in there
        $this->object = $this->getMockForAbstractClass(
            'Cmsgarden\Cmsscanner\Command\AbstractDetectionCommand',
            array("Commandname")
        );
    }

    public function testConstructorAddsAllAdapters()
    {
        $this->assertArrayHasKey('Joomla', $this->object->getAdapters());
        $this->assertArrayHasKey('WordPress', $this->object->getAdapters());
        $this->assertArrayHasKey('Drupal', $this->object->getAdapters());
        $this->assertArrayHasKey('TYPO3 CMS', $this->object->getAdapters());
        $this->assertArrayHasKey('Prestashop', $this->object->getAdapters());
        $this->assertArrayHasKey('Contao', $this->object->getAdapters());
        $this->assertArrayHasKey('Contenido', $this->object->getAdapters());
        $this->assertArrayHasKey('PivotX', $this->object->getAdapters());
        $this->assertArrayHasKey('Concrete5', $this->object->getAdapters());
        $this->assertCount(18, $this->object->getAdapters());
    }

    public function testAddingAnAdapter()
    {
        $this->object->addAdapter(new TestAdapter());

        $this->assertArrayHasKey('Test', $this->object->getAdapters());
    }
}
