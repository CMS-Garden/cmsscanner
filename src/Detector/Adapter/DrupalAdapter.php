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
 * Class DrupalAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class DrupalAdapter implements AdapterInterface
{
    /**
     * Joomla has changed the way how the version number is stored multiple times, so we need this comprehensive array
     * @var array
     */
    private $versions = array(
        array(
            "file" => "/modules/system/system.info",
            "regex" => "/version\\s*=\\s*\"(\\d\\.[^']+)\"[\\s\\S]*project\\s*=\\s*\"drupal\"/"
        ),
        array(
            "file" => "/core/modules/system/system.info.yml",
            "regex" => "/version:\\s*'(\\d\\.[^']+)'[\\s\\S]*project:\\s*'drupal'/"
        )
    );

    /**
     * Drupal has, depending on the version either a file called system.info or system.info.yaml
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('system.info')
            ->contains('project = "drupal"')
            ->name('system.info.yml')
            ->contains("project: 'drupal'");

        return $finder;
    }

    /**
     * try to verify a search result
     *
     * @param   SplFileInfo  $file  file to examine
     *
     * @return  bool|System
     */
    public function detectSystem(SplFileInfo $file)
    {
        if ($file->getFilename() == "system.info" && stripos($file->getContents(), 'project = "drupal"') !== false) {
            $path = new \SplFileInfo(dirname(dirname($file->getPath())));

            return new System($this->getName(), $path);
        }

        if ($file->getFilename() == "system.info.yml" && stripos($file->getContents(), "project: 'drupal'") !== false) {
            $path = new \SplFileInfo(dirname(dirname(dirname($file->getPath()))));

            return new System($this->getName(), $path);
        }

        return false;
    }

    /**
     * determine version of a Joomla installation within a specified path
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
                throw new \RuntimeException(sprintf("Unreadable version file %s", $versionFile));
            }

            preg_match($version['regex'], file_get_contents($versionFile), $matches);

            if (!count($matches)) {
                continue;
            }

            return $matches[1];
        }

        return null;
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Drupal';
    }
}
