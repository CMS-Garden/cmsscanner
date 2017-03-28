<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Command;

use Cmsgarden\Cmsscanner\Detector\Adapter\AdapterInterface;
use Cmsgarden\Cmsscanner\Detector\Adapter\ContaoAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\DrupalAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\JoomlaAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\PrestashopAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\Typo3CmsAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\WordpressAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\PivotxAdapter;
use Symfony\Component\Console\Command\Command;

/**
 * abstract class used to set up all the system adapters
 *
 * Class DetectionCommand
 * @package Cmsgarden\Cmsscanner\Command
 *
 * @since   1.0.0
 */
abstract class AbstractDetectionCommand extends Command
{
    /**
     * @var AdapterInterface[]
     */
    protected $adapters = array();

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this
            ->addAdapter(new ContaoAdapter())
            ->addAdapter(new DrupalAdapter())
            ->addAdapter(new JoomlaAdapter())
            ->addAdapter(new PrestashopAdapter())
            ->addAdapter(new Typo3CmsAdapter())
            ->addAdapter(new WordpressAdapter())
            ->addAdapter(new PivotxAdapter())
        ;
    }

    /**
     * Registers a detector engine implementation.
     *
     * @param   AdapterInterface  $adapter  An adapter instance
     *
     * @return  AbstractDetectionCommand  The current AbstractDetectionCommand instance
     */
    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[$adapter->getName()] = $adapter;

        return $this;
    }

    /**
     * Returns the currently registered adapters
     *
     * @return  array
     */
    public function getAdapters()
    {
        return $this->adapters;
    }
}
