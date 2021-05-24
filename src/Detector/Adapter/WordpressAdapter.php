<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Cmsgarden\Cmsscanner\Detector\Module;

/**
 * Class WordpressAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class WordpressAdapter implements AdapterInterface
{
    /**
     * The path of the plugins.
     * @var string
     */
    private $plugPath = 'wp-content/plugins';

    /**
     * Look for the version.php with a wp_version string in it
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('version.php');

        return $finder;
    }

    /**
     * verify a search result by making sure that the file has the correct name and $wp_version is in there
     *
     * @param   SplFileInfo  $file  file to examine
     *
     * @return  bool|System
     */
    public function detectSystem(SplFileInfo $file)
    {
        if ($file->getFilename() != "version.php") {
            return false;
        }

        if (stripos($file->getContents(), '$wp_version =') === false) {
            return false;
        }

        $path = new \SplFileInfo(dirname($file->getPath()));

        return new System($this->getName(), $path);
    }

    /**
     * determine version number of a WordPress installation
     *
     * @param   \SplFileInfo  $path  directory where the system is installed
     *
     * @return  null
     */
    public function detectVersion(\SplFileInfo $path)
    {
        $versionFile = $path . "/wp-includes/version.php";

        if (!file_exists($versionFile) || !is_readable($versionFile)) {
            return null; // @codeCoverageIgnore
        }

        preg_match("/\\\$wp_version\\s*=\\s*'([^']+)'/", file_get_contents($versionFile), $matches);

        if (count($matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @InheritDoc
     * This solution needs a refactoring!
     */
    public function detectModules(\SplFileInfo $path)
    {
        $modules = array();
        $matches = array();

        $pluginsubfolders = glob(sprintf('%s/%s/*', $path->getRealPath(), $this->plugPath), GLOB_ONLYDIR);
        $pluginmainfolder = glob(sprintf('%s/wp-content/plugins', $path->getRealPath()), GLOB_ONLYDIR);
        $folders =  array_merge($pluginsubfolders, $pluginmainfolder);

        foreach ($folders as $dir) {
            $done = false;
            if (array_key_exists($dir, $matches) === false) {
                $matches[$dir] = array();
            }

            foreach (glob(sprintf('%s/*.php', $dir)) as $plugin) {
                $name = null;
                $version = null;
                $content = file_get_contents($plugin);

                preg_match('/\s*Plugin Name:\s*(.*)/', $content, $name);
                preg_match('/\s*Version:\s*([\w._-]+)/', $content, $version);

                if (empty($name) === false && empty($version) === false) {
                    $modules[] = new Module($name[1], $dir, $version[1], 'plugin');
                    $done = true;
                    break;
                } else {
                    if (empty($name) === true) {
                        $name = null;
                    }

                    if (empty($version) === true) {
                        $version = null;
                    }

                    $matches[$dir][] = new Module($name, $dir, $version, 'plugin');
                }
            }

            if ($done === false) {
                $name = null;
                $version = null;
                foreach ($matches[$dir] as $possible) {
                    if ($possible->name !== null) {
                        $name = $possible->name;
                    }
                    if ($possible->version !== null) {
                        $version = $possible->version;
                    }
                }
                if ($name === null) {
                    $name = pathinfo($dir, PATHINFO_FILENAME);
                }
                if ($version === null) {
                    $version = 'unknown';
                }
                $modules[] = new Module($name, $dir, $version, 'plugin');
            }
        }
        return $modules;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'WordPress';
    }
}
