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

/**
 * Class RedaxoAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class RedaxoAdapter implements AdapterInterface
{
    /**
     * $REX['VERSION'] = "4";
     * $REX['SUBVERSION'] = "1";
     * $REX['MINORVERSION'] = "0";
     * $REX['MYSQL_VERSION'] = "";
     * @var array
     */
    private $versions = array(
        array(
            "file" => "/redaxo/include/master.inc.php",
            // @codingStandardsIgnoreLine
            "regex" => '/\$REX\[\'VERSION\'\]\s+= \"(\d+)\";\s+\$REX\[\'SUBVERSION\'\]\s+= \"(\d+)\";\s+\$REX\[\'MINORVERSION\'\]\s+= \"(\d+)\"/',
        ),
    );

    /**
     * Redaxo has, depending on the version either a file called system.info or system.info.yaml
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('master.inc.php');

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
        $test = $file->getPathInfo()->getPathInfo();
        if ($test->getFilename() == "redaxo" && file_exists($test->getPath())) {
            return new System($this->getName(), $test->getPathInfo());
        }
        return false;
    }

    /**
     * determine version of a Redaxo installation within a specified path
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
            unset($matches[0]);
            return implode('.', $matches);
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

    /***
     * @return string
     */
    public function getName()
    {
        return 'Redaxo';
    }
}
