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
 * Class ContaoAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 * @author Anton Dollmaier <ad@aditsystems.de>
 */
class ContaoAdapter implements AdapterInterface
{

    /**
     * Version detection information for Contao
     * @var array
     */
    protected $versions = array(
        array( // Contao 2.x
            'filename' => '/system/constants.php',
	    'regexp' => '/define\\(\'VERSION\', \'(.+)\'\\)/'
        ),
        array( // Contao 3.x
            'filename' => '/system/config/constants.php',
            'regexp' => '/define\\(\'VERSION\', \'(.+)\'\\)/'
        ),
    );

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
        $fileName = $file->getFilename();
        if ($fileName !== "constants.php" ) {
            return false;
	}
	if (stripos($file->getContents(), 'Contao') === false) {
	    return false;
	}
	if ( basename($file->getPath()) === 'system' ) {
	    // Contao 2.x
	    $path = new \SplFileInfo($file->getPathInfo()->getPath());
	} else {
	    $path = new \SplFileInfo(dirname($file->getPathInfo()->getPath()));
	}

        // Return result if working
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
            $sysEnvBuilder = $path->getRealPath() . $version['filename'];
            if (!file_exists($sysEnvBuilder)) {
                continue;
            }
            if (!is_readable($sysEnvBuilder)) {
                throw new \RuntimeException(sprintf("Unreadable version information file %s", $sysEnvBuilder));
	    }
	    if (preg_match($version['regexp'], file_get_contents($sysEnvBuilder), $matches)) {
                if (count($matches) > 1) {
                    return $matches[1];
                }
            }
        }
        // this must not happen usually
        return null;
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Contao';
    }
}
