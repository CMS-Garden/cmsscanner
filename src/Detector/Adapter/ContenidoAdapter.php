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

class ContenidoAdapter implements AdapterInterface
{

    private $versions = array(
        array(
            'file'      => 'startup.php',
            'regex'     =>  '/CON_VERSION.*4.9.7/',
            'version'   => 'CONTENIDO Version 4.9.8 or CONTENIDO Version 4.9.7'
        ), array(
            'file'      => 'startup.php',
            'regex'     => '/CON_VERSION.*4.9.6/',
            'version'   => 'CONTENIDO Version 4.9.6'
        ), array(
            'file'      => 'startup.php',
            'regex'     => '/CON_VERSION.*4.9.5/',
            'version'   => 'CONTENIDO Version 4.9.5'
        ), array(
            'file'      => 'startup.php',
            'regex'     => '/CON_VERSION.*4.9.4/',
            'version'   => 'CONTENIDO Version 4.9.4'
        ), array(
            'file'      => 'startup.php',
            'regex'     => '/CON_VERSION.*4.9.3/',
            'version'   => 'CONTENIDO Version 4.9.3'
        ), array(
            'file'      => 'startup.php',
            'regex'     => '/CON_VERSION.*4.9.2/',
            'version'   => 'CONTENIDO Version 4.9.2'
        ), array(
            'file'      => 'startup.php',
            'regex'     => '/CON_VERSION.*4.9.1/',
            'version'   => 'CONTENIDO Version 4.9.1'
        ), array(
            'file'       => 'contenido.css',
            'regex'      => '/opacity: 0.3 !important;/',
            'version'    => 'CONTENIDO Version 4.9.0'
        ), array(
            'file'       => 'template.info.html',
            'regex'      => '/Alexander Scheider/',
            'version'    => 'CONTENIDO Version 4.9.0 RC 1'
        ), array(
            'file'       => 'rowMark.js',
            'regex'      => '/Use jQuery .position()/',
            'version'    => 'CONTENIDO Version 4.9.0 Alpha 3'
        ), array(
            'file'       => 'navigation.xml',
            'regex'      => '/CONTENIDO XML language file/',
            'version'    => 'CONTENIDO Version 4.9.0 Alpha 1'
        ), array(
            'file'       => 'template.str_overview.html',
            'regex'      => '/\<div class=\"cat_label\"\>i18n\(\"Template\"\)\<\/div\>/',
            'version'    => 'CONTENIDO Version 4.8.19'
        ), array(
            'file'       => 'template.lang_left_top.html',
            'regex'      => '/dropList/',
            'version'    => 'CONTENIDO Version 4.8.18'
        ), array(
            'file'       => 'template.newsletter_left_top.html',
            'regex'      => '/i18n\(\"New group\"\)/',
            'version'    => 'CONTENIDO Version 4.8.17'
        ), array(
            'file'       => 'subnavi.js',
            'regex'      => '/_reset/',
            'version'    => 'CONTENIDO Version 4.8.16'
        ), array(
            'file'       => 'template.default_subnav.html',
            'regex'      => '/\<style type=\"text\/css\"\>/',
            'version'    => 'CONTENIDO Version 4.8.14 or CONTENIDO Version 4.8.15'
        )
    );

    /**
     * CONTENIDO has a file called startup.php that can be used to search for working installations
     *
     * @param   Finder $finder finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('startup.php');

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
        foreach ($this->versions as $version) {
            if (in_array($file->getFilename(), $version)) {
                // Check file content of startup.php to prevent false positives
                if (stripos($file->getContents(), "CONTENIDO") === false
                    && stripos($file->getContents(), "4fb.de") === false
                    && stripos($file->getContents(), "@author dirk.eschler") === false
                ) {
                    continue;
                }

                $path = new \SplFileInfo($file->getPath());

                // Return result if working
                return new System($this->getName(), $path);
            }
        }

        return false;
    }

    /**
     * determine version of a CONTENIDO installation within a specified path
     *
     * @param   \SplFileInfo $path directory where the system is installed
     *
     * @return  null|string
     */
    public function detectVersion(\SplFileInfo $path)
    {
        // Iterate through version patterns
        foreach ($this->versions as $version) {
            $versionFile = $path->getRealPath() . DIRECTORY_SEPARATOR . $version['file'];

            if (!file_exists($versionFile)) {
                continue;
            }

            if (!is_readable($versionFile)) {
                continue; // @codeCoverageIgnore
            }

            if (array_key_exists('regex', $version)) {
                preg_match($version['regex'], file_get_contents($versionFile), $matches);

                if (!count($matches)) {
                    continue;
                }

                return $version['version'];
            }
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
     * Name of the system
     *
     * @return string
     */
    public function getName()
    {
        return 'Contenido';
    }
}
