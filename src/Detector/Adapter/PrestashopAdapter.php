<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2017 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class PrestashopAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 * @author Anton Dollmaier <ad@aditsystems.de>
 */
class PrestashopAdapter implements AdapterInterface
{

    /**
     * Version detection information for Prestashop
     * @var array
     */
    protected $versions = array(
        array(
            'filename' => '/config/settings.inc.php',
            'regexp' => '/define\\(\'_PS_VERSION_\', \'(.+)\'\\)/'
        ),
    );

    /**
     * Prestashop has a file called constants.php that can be used to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('settings.inc.php');
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

        if ($fileName !== "settings.inc.php") {
            return false;
        }

        if (stripos($file->getContents(), '_PS_VERSION_') === false) {
                return false;
        }

        $path = new \SplFileInfo($file->getPathInfo()->getPath());

        // Return result if working
        return new System($this->getName(), $path);
    }

    /**
     * determine version of a Prestashop installation within a specified path
     *
     * @param   \SplFileInfo  $path  directory where the system is installed
     *
     * @return  null|string
     */
    public function detectVersion(\SplFileInfo $path)
    {
        foreach ($this->versions as $version) {
            $versionFile = $path->getRealPath() . $version['filename'];

            if (!file_exists($versionFile)) {
                continue;
            }

            if (!is_readable($versionFile)) {
                continue;
            }

            if (preg_match($version['regexp'], file_get_contents($versionFile), $matches)) {
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
        return 'Prestashop';
    }
}
