<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2016 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       http://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector;

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
     * @var string The path of the module.
     */
    public $path;

    /**
     * @var string The version of the module.
     */
    public $version;

    /**
     * Ctor.
     *
     * @param string $name    The name of the module.
     * @param string $path    The path of the module.
     * @param string $version The version of the module.
     */
    public function __construct($name, $path, $version)
    {
        $this->name    = $name;
        $this->path    = $path;
        $this->version = $version;
    }

    /**
     * Convert object to assoc array.
     *
     * @return array The data of the object as array.
     */
    public function toArray()
    {
        return array(
            'name' => $this->name,
            'version' => $this->version,
            'path' => $this->path
        );
    }
}
