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
 * Interface AdapterInterface
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 */
interface AdapterInterface
{
    /**
     * Add criteria to the finder which help the detection of the system
     *
     * @param Finder $finder
     * @return Finder
     */
    public function appendDetectionCriteria(Finder $finder);

    /**
     * Check a given file whether this is a known system
     *
     * @param SplFileInfo $file File to examine
     * @return bool|System Returns a System object or FALSE if no system was found
     */
    public function detectSystem(SplFileInfo $file);

    /**
     * Determine version of an installed system within a specified path
     *
     * @param \SplFileInfo $path Directory where the system is installed
     * @return null|string
     */
    public function detectVersion(\SplFileInfo $path);

    /**
     * Name of the system
     *
     * @return string
     */
    public function getName();
}
