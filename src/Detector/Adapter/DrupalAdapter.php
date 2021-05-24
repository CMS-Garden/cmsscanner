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
 * Class DrupalAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class DrupalAdapter implements AdapterInterface
{
    /**
     * Drupal has changed the way how the version number is stored multiple times, so we need this comprehensive array
     * @var array
     */
    private $versions = array(
        array(
            'file' => '/modules/system/system.info',
            'regex' => "/version\\s*=\\s*\"(\\d\\.[^']+)\"[\\s\\S]*project\\s*=\\s*\"drupal\"/"
        ),
        array(
            'file' => '/core/modules/system/system.info.yml',
            'regex' => "/version:\\s*'(\\d\\.[^']+)'[\\s\\S]*project:\\s*'drupal'/"
        )
    );

    /** @var string Major version of Drupal 7, 8 etc. */
    private $majorVersion;

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
            ->name('system.info.yml');

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
        if ($file->getFilename() == 'system.info' && stripos($file->getContents(), 'project = "drupal"') !== false) {
            $path = new \SplFileInfo(dirname(dirname($file->getPath())));

            return new System($this->getName(), $path);
        }

        if ($file->getFilename() == 'system.info.yml' && stripos($file->getContents(), "project: 'drupal'") !== false) {
            $path = new \SplFileInfo(dirname(dirname(dirname($file->getPath()))));

            return new System($this->getName(), $path);
        }

        return false;
    }

    /**
     * determine version of a Drupal installation within a specified path
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
        $this->majorVersion = substr($this->detectVersion($path), 0, 1);
        $mask = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\.info\.yml$/';
        $seperator = ':';

        if ((int) $this->majorVersion < 8) {
            $seperator = ' =';
            $mask = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\.info$/';
        }

        $files = $this->collectFiles($path, $mask, $seperator);
        $modules = array();

        foreach ($files as $file) {
            $contents = file_get_contents($file->uri);

            if (preg_match_all('/project' . $seperator . ' (.+)/', $contents, $matches) &&
              preg_match_all('/version' . $seperator . '(.+' . $this->majorVersion . '\.x-.+)/', $contents, $verMatches)
            ) {
                $matches = array_reverse($matches[1]);
                $verMatches = array_reverse($verMatches[1]);
                $project = trim($matches[0], '"\' ');
                $version = trim($verMatches[0], '"\' ');

                // Skip core modules and dev-version.
                if ($project != 'drupal' && strrpos($version, '-dev', -1) === false) {
                    $type = (strpos($file->uri, 'themes') !== false) ? 'theme' : 'module';
                    $modules[] = new Module($project, dirname($file->uri), $version, $type);
                }
            }
        }
        return $modules;
    }

    /**
     * Returns all possible directories where contrib module/theme can be stored.
     *
     * @param string $path The path from cli.
     *
     * @return array
     *   An array of existing directories.
     */
    protected function getSearchDirectories($path)
    {
        $searchDirs = array(
          'modules/*',
          'themes/*',
          'profiles/*/*',
          'sites/*/themes/*',
          'sites/*/modules/*',
        );

        $dirs = array();

        foreach ($searchDirs as $searchDir) {
            $searchDirPattern = $path.'/'.$searchDir;

            foreach (glob($searchDirPattern, GLOB_ONLYDIR) as $dir) {
                $dirs[] = $dir;
            }
        }
        return $dirs;
    }

    /**
     * Collects all info files.
     *
     * @param string $path The path from cli.
     * @param string $mask The info file mask.
     * @param string $seperator The key/value seperator in the info file.
     *
     * @return array
     */
    protected function collectFiles($path, $mask, $seperator)
    {
        $files = array();

        foreach ($this->getSearchDirectories($path) as $dir) {
            $files_to_add = $this->findFiles($dir, $mask);
            foreach ($files_to_add as $file_key => $file) {
                if ($this->checkCoreValueInInfoFile($file, $seperator)) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    /**
     * Checks that the core is in proper version.
     *
     * @param \stdClass $file The current info file.
     * @param string $seperator The key/value seperator in the info file.
     *
     * @return boolean
     */
    protected function checkCoreValueInInfoFile($file, $seperator)
    {
        if (preg_match('/core'.$seperator.'(.+)/', file_get_contents($file->uri), $matches)) {
            $version = trim($matches[1], '"\' ');

            if ($version === $this->majorVersion . '.x') {
                return true;
            }
        }

        return false;
    }

    /**
     * Find all files based on the mask.
     *
     * @param string $dir The subdirectory name in which the files are found.
     *    For example, 'modules' will search in sub-directories of the top-level /modules directory,
     *    sub-directories of /sites/all/modules/, etc.
     * @param string $mask The preg_match() regular expression for the files to find.
     *
     * @return array
     *   An associative array of file objects, keyed with the name of file
     *   without the extension. Each element in the array is an object containing file information,
     *   with properties:
     *    - uri: Full URI of the file.
     *    - filename: File name.
     *    - name: Name of file without the extension.
     */
    protected function findFiles($dir, $mask)
    {
        $files = array();

        if (is_dir($dir) && $handle = opendir($dir)) {
            while (false !== ($filename = readdir($handle))) {
                if (!preg_match('/(\.\.?|CVS|test(s)?)$/i', $filename) && $filename[0] != '.') {
                    $uri = "$dir/$filename";

                    if (is_dir($uri)) {
                        // Give priority to files in this folder by merging them in after any subdirectory files.
                        $files = array_merge($this->findFiles($uri, $mask), $files);
                    } elseif (preg_match($mask, $filename)) {
                        $file = new \stdClass();
                        $file->uri = $uri;
                        $file->filename = $filename;
                        $file->name = pathinfo($filename, PATHINFO_FILENAME);
                        $files[$file->name] = $file;
                    }
                }
            }

            closedir($handle);
        }
        return $files;
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Drupal';
    }
}
