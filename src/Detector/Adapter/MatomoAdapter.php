<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2019 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MatomoAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class MatomoAdapter implements AdapterInterface
{
    /**
     * Matomo has changed the way how the version number is stored multiple times, so we need this comprehensive array
     * @var array
     */
    private $versions = array(
        array(
            "file" => "/core/Version.php",
            "regex" => '~VERSION = \'([0-9\.]+)\'~',
        ),
    );

    /**
     * Matomo has a file called configuration.php that can be used to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('piwik.js');
        return $finder;
    }

    /**
     * try to verify a search result and work around some well known false positives
     *
     * @param   SplFileInfo  $file  file to examine
     *
     * @return  bool|System
     */
    public function detectSystem(SplFileInfo $file)
    {
        if ($file->getFilename() != "piwik.js" && $file->getFilename() != "matomo.js") {
            return false;
        }
        if (file_exists($file->getPath().'/js/piwik.js')) {
            $path = new \SplFileInfo($file->getPath());
        } elseif (file_exists($file->getPath().'/js/matomo.js')) {
            $path = new \SplFileInfo($file->getPath());
        } else {
            return false;
        }

        // Return result if working
        return new System($this->getName(), $path);
    }

    /**
     * determine version of a Matomo installation within a specified path
     *
     * @param   \SplFileInfo  $path  directory where the system is installed
     *
     * @return  null|string
     */
    public function detectVersion(\SplFileInfo $path)
    {
        // Iterate through version patterns
        foreach ($this->versions as $version) {
            $versionFile = $path->getRealPath() . $version['file'];

            if (!file_exists($versionFile)) {
                continue;
            }

            if (!is_readable($versionFile)) {
                continue; // @codeCoverageIgnore
            }

            preg_match($version['regex'], file_get_contents($versionFile), $matches);

            if (!count($matches)) {
                continue;
            }
            return $matches[1];
        }

        return null;
    }

    /**
     * @InheritDoc
     */
    public function detectModules(\SplFileInfo $path)
    {
        // TODO implement this function
        return false;
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Matomo';
    }
}
