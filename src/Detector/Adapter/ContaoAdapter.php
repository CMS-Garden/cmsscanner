<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\Module;
use Cmsgarden\Cmsscanner\Detector\System;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ContaoAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 * @author Andreas Schempp <https://github.com/aschempp>
 */
class ContaoAdapter implements AdapterInterface
{
    /**
     * Possible files to detect a Contao version
     * @var array
     */
    private static $paths = array(
        '/system/constants.php',
        '/system/config/constants.php',
        '/vendor/contao/core-bundle/Resources/contao/config/constants.php',
    );

    private static $coreModules = array(
        'backend',
        'frontend',
        'core',
        'calendar',
        'comments',
        'devtools',
        'faq',
        'listing',
        'news',
        'newsletter',
        'repository',
    );

    /**
     * Regular expression to read the Contao version from constants.php
     * @var string
     */
    private $versionRegexp = '/define\\(\'VERSION\', \'(\d\.\d{1,2})\'\\)/';

    /**
     * Regular expression to read the Contao build from constants.php
     * @var string
     */
    private $buildRegexp = '/define\\(\'BUILD\', \'(\d)\'\\)/';


    /**
     * Contao has a file called constants.php that can be used to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('constants.php');

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
        if ("constants.php" !== $file->getFilename()) {
            return false;
        }

        if (stripos($file->getContents(), 'Contao') === false) {
            return false;
        }

        foreach (static::$paths as $version) {
            $path   = $file->getPathname();
            $length = strlen($version) * -1;

            if (substr($path, $length) === $version) {
                return new System(
                    $this->getName(),
                    new \SplFileInfo(substr($path, 0, $length))
                );
            }
        }

        return false;
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
        foreach (static::$paths as $version) {
            $versionFile = $path->getRealPath() . $version;

            if (!file_exists($versionFile)) {
                continue;
            }

            if (!is_readable($versionFile)) {
                continue;
            }

            $fileContents = file_get_contents($versionFile);
            $version       = null;
            $build         = null;

            if (preg_match($this->versionRegexp, $fileContents, $matches) && count($matches) > 1) {
                $version = $matches[1];

                if (preg_match($this->buildRegexp, $fileContents, $matches) && count($matches) > 1) {
                    $build = $matches[1];

                    return $version . '.' . $build;
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
        $modules = array();

        $this->detectContaoModules($path, $modules);
        $this->detectComposerModules($path->getPathname() . '/composer/vendor', $modules);
        $this->detectComposerModules($path->getPathname() . '/vendor', $modules);

        return $modules;
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Contao';
    }

    /**
     * Finds all Contao modules in system/modules folder
     *
     * @param \SplFileInfo $path
     * @param Module[]     $modules
     *
     * @throws \InvalidArgumentException
     */
    private function detectContaoModules(\SplFileInfo $path, array &$modules)
    {
        $finder = Finder::create()
            ->directories()
            ->depth(0)
            ->in($path->getPathname() . '/system/modules')
        ;

        /** @var SplFileInfo $dir */
        foreach ($finder as $dir) {
            if (in_array($dir->getBasename(), static::$coreModules, true)) {
                continue;
            }

            $modules[] = new Module($dir->getBasename(), $dir->getPathname(), '');
        }
    }

    /**
     * @param string   $vendorDir
     * @param Module[] $modules
     */
    private function detectComposerModules($vendorDir, array &$modules)
    {
        $installedJson = $vendorDir . '/composer/installed.json';

        if (!file_exists($installedJson)) {
            return;
        }

        $bundles = json_decode(file_get_contents($installedJson), true);

        foreach ($bundles as $bundle) {
            if ('contao-bundle' === $bundle['type']) {
                $modules[] = $this->createModuleFromBundle($vendorDir, $bundle);
                continue;
            }

            if ('contao-module' !== $bundle['type']
                && 'legacy-contao-module' !== $bundle['type']
            ) {
                continue;
            }

            // Try to guess if the bundle was installed in system/modules
            list($vendor, $name) = explode('/', $bundle['name']);

            foreach ($modules as $module) {
                if ($name === $module->name
                    || (strpos($name, 'contao-') === 0 && substr($name, 7) === $module->name)
                ) {
                    $module->version = $bundle['version'];
                    continue(2);
                }
            }

            $modules[] = $this->createModuleFromBundle($vendorDir, $bundle);
        }
    }

    private function createModuleFromBundle($vendorDir, array $bundle)
    {
        return $modules[] = new Module($bundle['name'], $vendorDir . '/' . $bundle['name'], $bundle['version']);
    }
}
