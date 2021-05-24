<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 * @package Cmsgarden\Cmsscanner\Command
 *
 * @since   1.0.0
 */
class UpdateCommand extends Command
{
    const MANIFEST_FILE = 'https://cms-garden.github.io/cmsscanner/manifest.json';

    /**
     * configure this console command
     */
    protected function configure()
    {
        $this
            ->setName('cmsscanner:update')
            ->setDescription('Updates cmsscanner.phar to the latest version')
        ;
    }

    /**
     * execute this command
     *
     * @param   InputInterface   $input   CLI input data
     * @param   OutputInterface  $output  CLI output data
     *
     * @return  int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}
