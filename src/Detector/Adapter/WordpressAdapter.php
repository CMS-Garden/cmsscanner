<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class WordpressAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class WordpressAdapter implements AdapterInterface
{
    /**
     * look for the version.php with a wp_version string in it
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
            return null;
        }

        preg_match("/\\\$wp_version\\s*=\\s*'([^']+)'/", file_get_contents($versionFile), $matches);

        if (count($matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'WordPress';
    }
}