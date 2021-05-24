<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Cmsgarden\Cmsscanner\Detector\Module;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class AlchemyCmsAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class AlchemyCmsAdapter implements AdapterInterface
{

    # The Gemfile.lock includes the fixed version number of Alchemy CMS
    # the host application is curently using.
    protected $version_file = "Gemfile.lock";
    protected $version_regex = "/\s{2,}specs:\n\s{4,}alchemy_cms\s\((\d+\.\d+\..*)\)\n/";

    /**
     * look for the Gemfile.lock with a proper version format
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name($this->version_file);

        return $finder;
    }

    /**
     * verify a search result by making sure that the file has the correct name and alchemy_cms is in there
     *
     * @param   SplFileInfo  $file  file to examine
     *
     * @return  bool|System
     */
    public function detectSystem(SplFileInfo $file)
    {
        if ($file->getFilename() != $this->version_file) {
            return false;
        }

        if (stripos($file->getContents(), 'alchemy_cms') === false) {
            return false;
        }

        $path = new \SplFileInfo($file->getPath());

        return new System($this->getName(), $path);
    }

    /**
     * determine version number of a Alchemy CMS installation
     *
     * @param   \SplFileInfo  $path  directory where the system is installed
     *
     * @return  null
     */
    public function detectVersion(\SplFileInfo $path)
    {
        $versionFile = $path->getRealPath() . "/" . $this->version_file;

        if (!file_exists($versionFile) || !is_readable($versionFile)) {
            return null; // @codeCoverageIgnore
        }

        preg_match($this->version_regex, file_get_contents($versionFile), $matches);

        if (count($matches)) {
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'Alchemy CMS';
    }
}
