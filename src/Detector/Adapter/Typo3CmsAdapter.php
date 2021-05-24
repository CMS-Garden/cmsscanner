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
 * Class Typo3CmsAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 * @author Markus Klein <klein.t3@reelworx.at>
 */
class Typo3CmsAdapter implements AdapterInterface
{

    /**
     * Version detection information for TYPO3 CMS 4.x and 6.x
     * @var array
     */
    protected $versions = array(
        array( // 6.x
            'filename' => '/typo3/sysext/core/Classes/Core/SystemEnvironmentBuilder.php',
            'regexp' => '/define\\(\'TYPO3_version\', \'(.*?)\'\\)/'
        ),
        array( // 4.x
            'filename' => '/t3lib/config_default.php',
            'regexp' => '/TYPO_VERSION = \'(.*?)\'/'
        ),
    );

    /**
     * TYPO3 has a file called LocalConfiguration.php or localconf.php that can be used
     * to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('LocalConfiguration.php');
        $finder->name('localconf.php');
        // always skip typo3temp as it may contain leftovers from functional tests
        $finder->notPath('typo3temp');
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
        if ($fileName !== "LocalConfiguration.php" && $fileName !== 'localconf.php') {
            return false;
        }
        $path = new \SplFileInfo($file->getPathInfo()->getPath());

        // Return result if working
        return new System($this->getName(), $path);
    }

    /**
     * determine version of a TYPO3 CMS installation within a specified path
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
        // if the script comes here your TYPO3 environment is broken somehow
        // e.g. broken typo3_src symlink or the like
        return null;
    }

    /**
     * @InheritDoc
     */
    public function detectModules(\SplFileInfo $path)
    {
        $modules = array();
        $finder = new Finder();

        $finder->name('ext_emconf.php');
        foreach ($finder->in($path->getRealPath()) as $config) {
            preg_match("/\'title\'\s*?\=\>\s*?'([^']+)/", file_get_contents($config->getRealPath()), $titel);
            preg_match("/\'version\'\s*?\=\>\s*?'([^']+)/", file_get_contents($config->getRealPath()), $version);
            preg_match("/\'category\'\s*?\=\>\s*?'([^']+)/", file_get_contents($config->getRealPath()), $type);

            if (!count($titel)) {
                continue;
            }

            if (!count($version)) {
                $version[1] = 'unknown';
            }

            if (!count($type)) {
                $type[1] = 'unknown';
            }

            $modules[] = new Module($titel[1], $config->getRealPath(), $version[1], $type[1]);
        }

        // Remove the Core Extensions form the return array
        foreach ($modules as $key => $module) {
            // Remove real path
            $module->path = str_replace($path->getRealPath(), '', $module->path);

            if (strpos($module->path, '/typo3/sysext') === 0) {
                unset($modules[$key]);
            }
        }

        return array_values($modules);
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'TYPO3 CMS';
    }
}
