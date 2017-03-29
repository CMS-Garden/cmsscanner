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
 * Class Concrete5Adapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class Concrete5Adapter implements AdapterInterface
{
    /**
     * Version detection information for Contao
     * @var array
     */
    protected $versions = array(
        array( //
            'filename' => '/concrete/config/version.php',
            'regexp' => "/\\\$APP_VERSION\\s*=\\s*'([^']+)'/",
        ),
        array( // 8+ ??
            'filename' => '/concrete/config/concrete.php',
            'regexp' => "/'version' => '([^']+)'/",
        ),
    );
    /**
     * look for the version.php with a version string in it
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
      $finder->name('version.php')
          ->name('concrete.php');

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
        if ($file->getFilename() == "version.php") {
            if (stripos($file->getContents(), '$APP_VERSION =') === false) {
                return false;
          }
        }
        elseif ($file->getFilename() == "concrete.php") {
            if (stripos($file->getContents(), "'version' => '") === false) {
                return false;
            }
        }
        else {
            return false;
        }
        $path = new \SplFileInfo(dirname($file->getPath()));

        return new System($this->getName(), $path);
    }

    /**
     * determine version of a Contao installation within a specified path
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
                throw new \RuntimeException(sprintf("Unreadable version information file %s", $versionFile));
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
     * @return string
     */
    public function getName()
    {
        return 'Concrete5';
    }
}
