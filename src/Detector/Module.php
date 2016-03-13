<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Module
 *
 * A module can also be an extension, plugin, component or anything else additional.
 *
 * @package Cmsgarden\Cmsscanner\Detector
 *
 * @since   1.0.0
 */
class Module
{
    /**
     * @var string The name of the module.
     */
    public $name;

    /**
     * @var SplFileInfo The path of the module.
     */
    public $path;

    /**
     * @var string The version of the module.
     */
    public $version;

    /**
     * Ctor.
     *
     * @param string       $name    The name of the module.
     * @param SplFileInfo $path    The path of the module.
     * @param string       $version The version of the module.
     */
    public function __construct($name, SplPathInfo $path, $version)
    {
        $this->name    = $name;
        $this->path    = $path;
        $this->version = $version;
    }
}
