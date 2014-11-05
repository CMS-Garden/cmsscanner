<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Interface AdapterInterface
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 */
interface AdapterInterface
{
    public function appendDetectionCriteria(Finder $finder);

    public function detectSystem(SplFileInfo $file);

    public function detectVersion(\SplFileInfo $path);

    public function getName();
}