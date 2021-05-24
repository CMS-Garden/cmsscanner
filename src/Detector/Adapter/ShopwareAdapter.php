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

/**
 * Class ShopwareAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class ShopwareAdapter implements AdapterInterface
{

    /**
     * Version detection information for Shopware
     * @var array
     */
    protected $versions = array(
        array(
            'filename' => '/engine/Shopware/Application.php',
            'regexp' => '/const VERSION\s+= \'(.+)\';/'
        ),
    );

    /**
     * Shopware has a file called constants.php that can be used to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('Application.php');
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
        $fileName = $file->getFilename();
        if ($fileName !== "Application.php") {
            return false;
        }
        if (stripos($file->getContents(), 'class Shopware extends Enlight_Application') === false) {
            return false;
        }
        if (basename($file->getPath()) === 'Shopware') {
            // Shopware 2.x
            $path = new \SplFileInfo($file->getPathInfo()->getPathInfo()->getPath());
        }

        // Return result if working
        return new System($this->getName(), $path);
    }

    /**
     * determine version of a Shopware installation within a specified path
     *
     * @param   \SplFileInfo  $path  directory where the system is installed
     *
     * @return  null|string
     */
    public function detectVersion(\SplFileInfo $path)
    {
        foreach ($this->versions as $version) {
            $sysEnvBuilder = $path->getRealPath() . $version['filename'];
            if (!file_exists($sysEnvBuilder)) {
                continue;
            }
            if (!is_readable($sysEnvBuilder)) {
                throw new \RuntimeException(sprintf("Unreadable version information file %s", $sysEnvBuilder));
            }
            if (preg_match($version['regexp'], file_get_contents($sysEnvBuilder), $matches)) {
                if (count($matches) > 1) {
                    return $matches[1];
                }
            }
        }
        // this must not happen usually
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
        return 'Shopware';
    }
}
