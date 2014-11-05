<?php
/**
 * @version    1.0.0
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Command;

use Cmsgarden\Cmsscanner\Detector\Adapter\AdapterInterface;
use Cmsgarden\Cmsscanner\Detector\Adapter\JoomlaAdapter;
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
abstract class DetectionCommand extends Command
{
    protected $adapters = array();

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this
            ->addAdapter(new JoomlaAdapter())
            ->addAdapter(new WordpressAdapter())
        ;
    }

    /**
     * Registers a detector engine implementation.
     *
     * @param AdapterInterface $adapter  An adapter instance
     *
     * @return DetectionCommand The current DetectionCommand instance
     */
    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[$adapter->getName()] = $adapter;

        return $this;
    }
}
