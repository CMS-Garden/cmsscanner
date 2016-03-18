<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
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
        $this->assertCount(7, $this->object->getAdapters());
    }

    public function testAddingAnAdapter()
    {
        $this->object->addAdapter(new TestAdapter());

        $this->assertArrayHasKey('Test', $this->object->getAdapters());
    }
}
