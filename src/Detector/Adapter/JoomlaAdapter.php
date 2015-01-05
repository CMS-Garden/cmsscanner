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
 * Class JoomlaAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class JoomlaAdapter implements AdapterInterface
{
    /**
     * Joomla has changed the way how the version number is stored multiple times, so we need this comprehensive array
     * @var array
     */
    private $versions = array(
        array(
            "file" => "/libraries/joomla/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'1\\.5';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "1.5."
        ),
        array(
            "file" => "/libraries/joomla/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'1\\.6';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "1.6."
        ),
        array(
            "file" => "/includes/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'1\\.7';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "1.7."
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'2\\.5';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "2.5."
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'3\\.0';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "3.0."
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'3\\.1';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "3.1."
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'3\\.2';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "3.2."
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'3\\.3';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "3.3."
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'3\\.4';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "3.4."
        )
    );

    /**
     * Joomla has a file called configuration.php that can be used to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('configuration.php');

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
        if ($file->getFilename() != "configuration.php") {
            return false;
        }

        if (stripos($file->getContents(), "JConfig") === false) {
            return false;
        }

        // False positive "Akeeba Backup Installer"
        if (stripos($file->getContents(), "class ABIConfiguration") !== false) {
            return false;
        }

        // False positive mock file in unit test folder
        if (stripos($file->getContents(), "Joomla.UnitTest") !== false) {
            return false;
        }

        // False positive mock file in unit test folder
        if (stripos($file->getContents(), "Joomla\Framework\Test") !== false) {
            return false;
        }

        $path = new \SplFileInfo($file->getPath());

        // Return result if working
        return new System($this->getName(), $path);
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
                continue; // @codeCoverageIgnore
            }

            preg_match($version['regex'], file_get_contents($versionFile), $matches);

            if (!count($matches)) {
                continue;
            }

            return $version['minor'] . $matches[1];
        }

        return null;
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Joomla';
    }
}
