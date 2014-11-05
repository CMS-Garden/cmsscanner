<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class DetectCommand
 * @package Cmsgarden\Cmsscanner\Command
 *
 * @since   1.0.0
 */
class DetectCommand extends DetectionCommand
{
    /**
     * configure this console command
     */
    protected function configure()
    {
        $this
            ->setName('cmsscanner:detect')
            ->setDescription('Detects all CMS installations in a given path')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Directory where CMS should be detected'
            )
            ->addOption(
                'depth',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the detector will limit the directory recursion to the specified level'
            )
            ->addOption(
                'versions',
                null,
                InputOption::VALUE_NONE,
                'If set, the detector will determine the used version'
            )
        ;

    }

    /**
     * execute this command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create a new Finder instance
        $finder = new Finder();

        // Search for files in the directory specified in the CLI argument
        $finder->files()
            ->in($input->getArgument('path'));

        // Limit search to recursion level
        if ($input->getOption('depth')) {
            $finder->depth("<= " . $input->getOption('depth'));
        }

        // Append system specific search criterias
        foreach ($this->adapters as $adapterName => $adapter) {
            $finder = $adapter->appendDetectionCriteria($finder);
        }

        $results = array();

        // Iterate through results
        foreach ($finder as $file) {

            // Iterate through system adapters
            foreach ($this->adapters as $adapterName => $adapter) {

                // Pass the search result to the system adapter to verify the result
                if (!$system = $adapter->detectSystem($file)) {
                    // search result doesn't match with system
                    continue;
                }

                // If enabled, try to determine the used CMS version
                if ($input->getOption('versions')) {
                    $system->version = $adapter->detectVersion($system->getPath());
                }

                // Append successful result to array
                $results[] = $system;
            }
        }

        // Generate stats array
        $stats = $this->generateStats($results, $input->getOption('versions'));

        // Write output to command line
        $output->writeln('<info>Successfully finished scan!</info>');
        $output->writeln(sprintf('CMSScanner found %d CMS installations!', count($results)));

        // Write stats to command line
        $this->outputStats($stats, $input->getOption('versions'), $output);
    }

    /**
     * generate stats array from results
     *
     * @param array $results  results returned from system adapters
     * @param       $versionStats generate version stats
     *
     * @return array
     */
    protected function generateStats(array $results, $versionStats)
    {
        $stats = array();

        foreach ($results as $result) {
            $systemName = $result->getName();

            // Create stats array for each system
            if (empty($stats[$systemName])) {
                $stats[$systemName] = array(
                    'amount' => 0,
                    'versions' => array("Unknown" => 0)
                );
            }

            $stats[$systemName]['amount']++;

            // Increase count for this used version
            if ($versionStats) {
                if (!$result->version) {
                    $stats[$systemName]['versions']['Unknown']++;
                    continue;
                }

                if (empty($stats[$systemName]['versions'][$result->version])) {
                    $stats[$systemName]['versions'][$result->version] = 0;
                }

                $stats[$systemName]['versions'][$result->version]++;
            }
        }

        return $stats;
    }

    /**
     * output stats to command line
     *
     * @param array           $stats
     * @param                 $versionStats
     * @param OutputInterface $output
     */
    protected function outputStats(array $stats, $versionStats, OutputInterface $output)
    {
        $output->writeln("");

        $table = new Table($output);
        $table->setHeaders(array('CMS', '# Installations'));

        foreach ($stats as $system => $cmsStats) {
            $table->addRow(array($system, $cmsStats['amount']));
        }

        $table->render();

        // Render version stats if enabled
        if ($versionStats) {
            $output->writeln("");
            $output->writeln("<info>Version specific stats:</info>");

            foreach ($stats as $system => $cmsStats) {
                $output->writeln(sprintf("%s:", $system));

                $table = new Table($output);
                $table->setHeaders(array('Version', '# Installations'));

                foreach ($cmsStats['versions'] as $version => $amount) {
                    $table->addRow(array($version, $amount));
                }

                $table->render();
            }
        }
    }
}
