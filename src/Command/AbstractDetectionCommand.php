<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Command;

use Cmsgarden\Cmsscanner\Detector\Adapter\AdapterInterface;
use Cmsgarden\Cmsscanner\Detector\Adapter\AdminerAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\Concrete5Adapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\ContaoAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\ContenidoAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\DrupalAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\GambioAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\JoomlaAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\MagentoAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\MsdAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\NextcloudAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\PhpMyAdminAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\PivotxAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\MatomoAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\PrestashopAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\RedaxoAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\ShopwareAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\Typo3CmsAdapter;
use Cmsgarden\Cmsscanner\Detector\Adapter\WordpressAdapter;
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
            ->addAdapter(new AdminerAdapter())
            ->addAdapter(new Concrete5Adapter())
            ->addAdapter(new ContaoAdapter())
            ->addAdapter(new ContenidoAdapter())
            ->addAdapter(new DrupalAdapter())
            ->addAdapter(new GambioAdapter())
            ->addAdapter(new JoomlaAdapter())
            ->addAdapter(new MagentoAdapter())
            ->addAdapter(new MsdAdapter())
            ->addAdapter(new NextcloudAdapter())
            ->addAdapter(new PhpmyadminAdapter())
            ->addAdapter(new PivotxAdapter())
            ->addAdapter(new MatomoAdapter())
            ->addAdapter(new PrestashopAdapter())
            ->addAdapter(new RedaxoAdapter())
            ->addAdapter(new ShopwareAdapter())
            ->addAdapter(new Typo3CmsAdapter())
            ->addAdapter(new WordpressAdapter())
            ->addAdapter(new DrupalAdapter())
            ->addAdapter(new Typo3CmsAdapter())
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
