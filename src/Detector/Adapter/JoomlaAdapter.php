<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2016 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Cmsgarden\Cmsscanner\Detector\Module;
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
            "file" => "/includes/version.php",
            "regex" => "/\\\$RELEASE\\s*=\\s*'1\\.0';[\\s\\S]*\\\$DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "1.0."
        ),
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
        ),
        array(
            "file" => "/libraries/cms/version/version.php",
            "regex" => "/RELEASE\\s*=\\s*'3\\.5';[\\s\\S]*const\\s*DEV_LEVEL\\s*=\\s*'([^']+)'/",
            "minor" => "3.5."
        )
    );

    private $componentPaths = array(
        'components',
        'administrator/components'
    );

    private $modulePaths = array(
        'modules',
        'administrator/modules'
    );

    private $pluginPath = 'plugins';

    private $templatePaths = array(
        'templates',
        'administrator/templates'
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

        if (stripos($file->getContents(), "JConfig") === false
            && stripos($file->getContents(), 'mosConfig') === false) {
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

    /**
     * @InheritDoc
     */
    public function detectModules(\SplFileInfo $path)
    {
        $modules = array();

        foreach ($this->modulePaths as $mpath) {
            foreach (glob(sprintf('%s/%s/*', $path->getRealPath(), $mpath), GLOB_ONLYDIR) as $dir) {
                $infoFile = sprintf('%s/%s.xml', $dir, pathinfo($dir, PATHINFO_FILENAME));

                if (file_exists($infoFile)) {
                    $info = $this->parseXMLInfoFile($infoFile);
                    $modules[] = new Module($info['name'], $dir, $info['version'], 'module');
                }
            }
        }

        $this->detectComponents($path, $modules);
        $this->detectPlugins($path, $modules);
        $this->detectTemplates($path, $modules);

        return $modules;
    }

    /**
     * detects installed joomla components
     *
     * @param \SplFileInfo $path
     * @param array        $modules
     */
    private function detectComponents(\SplFileInfo $path, array &$modules)
    {
        foreach ($this->componentPaths as $cpath) {
            foreach (glob(sprintf('%s/%s/*', $path->getRealPath(), $cpath), GLOB_ONLYDIR) as $dir) {
                $filename = pathinfo($dir, PATHINFO_FILENAME);
                $filename = substr($filename, strpos($filename, '_') + 1);
                $infoFile = sprintf('%s/%s.xml', $dir, $filename);

                if (file_exists($infoFile)) {
                    $info = $this->parseXMLInfoFile($infoFile);
                    $modules[] = new Module($info['name'], $dir, $info['version'], 'component');
                }
            }
        }
    }

    /**
     * detects installed joomla plugins
     *
     * @param \SplFileInfo $path
     * @param array        $modules
     */
    private function detectPlugins(\SplFileInfo $path, array &$modules)
    {
        $foundPlugin = false;

        // search for plugins in Joomla > 1.5 first
        foreach (glob(sprintf('%s/%s/*/*', $path->getRealPath(), $this->pluginPath), GLOB_ONLYDIR) as $dir) {
            $infoFile = sprintf('%s/%s.xml', $dir, pathinfo($dir, PATHINFO_FILENAME));

            if (file_exists($infoFile)) {
                $info = $this->parseXMLInfoFile($infoFile);
                $modules[] = new Module($info['name'], $dir, $info['version'], 'plugin');

                $foundPlugin = true;
            }
        }

        // skip legacy plugin search if first step had been succesful
        if ($foundPlugin) {
            return;
        }

        // search for plugins in Joomla 1.5
        foreach (glob(sprintf('%s/%s/*/*.xml', $path->getRealPath(), $this->pluginPath)) as $infoFile) {
            if (file_exists($infoFile)) {
                $info = $this->parseXMLInfoFile($infoFile);
                $modules[] = new Module($info['name'], dirname($infoFile), $info['version'], 'plugin');
            }
        }
    }

    /**
     * detects installed joomla templates
     *
     * @param \SplFileInfo $path
     * @param array        $modules
     */
    private function detectTemplates(\SplFileInfo $path, array &$modules)
    {
        foreach ($this->templatePaths as $tpath) {
            foreach (glob(sprintf('%s/%s/*', $path->getRealPath(), $tpath), GLOB_ONLYDIR) as $dir) {
                $infoFile = sprintf('%s/templateDetails.xml', $dir);

                if (file_exists($infoFile)) {
                    $info = $this->parseXMLInfoFile($infoFile);
                    $modules[] = new Module($info['name'], $dir, $info['version'], 'template');
                }
            }
        }
    }

    /**
     * Parse an XML info file.
     *
     * @param string $file Full file path of the xml info file.
     *
     * @return array The data of the XML file.
     */
    private function parseXMLInfoFile($file)
    {
        $name = null;
        $version = null;
        $content = file_get_contents($file);

        if (preg_match('/<name>(.*)<\/name>/', $content, $matches)) {
            $name = $matches[1];
        }

        if (preg_match('/<version>(.*)<\/version>/', $content, $matches)) {
            $version = $matches[1];
        }

        return array('name' => $name, 'version' => $version);
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Joomla';
    }
}
