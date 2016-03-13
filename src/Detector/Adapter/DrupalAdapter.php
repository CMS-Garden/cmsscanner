<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
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
        $version = $this->detectVersion($path);
        $versionComp = substr($version, 0, 1);
        $searchDirs = array(
          'modules/*',
          'profiles/*',
          'sites/*/modules/*',
          'sites/*/themes/*',
        );
        $moduleMask = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\.module$/';
        $options = array(
          'keySeperator' => ':',
          'infoFile' => '.info.yml'
        );

        if ((int) $versionComp < 8) {
            $options = array(
              'keySeperator' => ' =',
              'infoFile' => '.info'
            );
            $searchDirs = array(
              'modules/*',
              'profiles/*/*',
              'sites/all/modules/*',
              'sites/all/themes/*',
              'sites/*/modules/*',
              'sites/*/themes/*',
            );
        }
        $files = array();
        foreach ($searchDirs as $searchDir) {
            $searchDirPattern = $path . '/' . $searchDir;
            foreach (glob($searchDirPattern, GLOB_ONLYDIR) as $dir) {
                $files_to_add = $this->findModuleFiles($dir, $moduleMask);
                foreach (array_intersect_key($files_to_add, $files) as $file_key => $file) {
                    // If it has no info file, then we just behave liberally and accept the
                    // new resource on the list for merging.
                    if (file_exists(
                      $info_file = dirname($file->uri). '/' . $file->name . $options['infoFile']
                    )) {
                        if (preg_match('/core' . $options['keySeperator'] . ' (.+)/', file_get_contents($info_file), $matches)) {
                            if ($matches[1] !== $versionComp . '.x') {
                                unset($files_to_add[$file->name]);
                            }
                        }
                    }


                }
                $files = array_merge($files, $files_to_add);
            }
        }
        $modules = array();
        foreach ($files as $file) {
            if (file_exists(
              $info_file = dirname($file->uri). '/' . $file->name . $options['infoFile']
            )) {
                if (preg_match('/project' . $options['keySeperator'] . ' (.+)/', file_get_contents($info_file), $matches) &&
                  preg_match('/version' . $options['keySeperator'] . ' (.+)/', file_get_contents($info_file), $verMatches)
                ) {
                    $project = trim($matches[1], '"');
                    $version = trim($verMatches[1], '"');
                    if ($project != 'drupal') {
                        $modules[$project] = new Module($project, dirname($file->uri), $version);
                    }
                }
            }

        }
        return $modules;
    }


    /**
     * Find all files based on the mask.
     *
     * @param string $dir The subdirectory name in which the files are found. For example, 'modules' will search in sub-directories of the top-level /modules directory, sub-directories of /sites/all/modules/, etc.
     * @param string $mask The preg_match() regular expression for the files to find.
     *
     * @return array
     *   An associative array of file objects, keyed on the chosen key. Each element in the array is an object containing file information, with properties:
     *    - uri: Full URI of the file.
     *    - filename: File name.
     *    - name: Name of file without the extension.
     */
    protected function findModuleFiles($dir, $mask) {
        $files = array();
        if (is_dir($dir) && $handle = opendir($dir)) {
            while (false !== ($filename = readdir($handle))) {
                if (!preg_match('/(\.\.?|CVS|tests)$/', $filename) && $filename[0] != '.') {
                    $uri = "$dir/$filename";
                    if (is_dir($uri)) {
                        // Give priority to files in this folder by merging them in after any subdirectory files.
                        $files = array_merge(
                          $this->findModuleFiles($uri, $mask),
                          $files
                        );
                    }
                    elseif (preg_match($mask, $filename)) {

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
