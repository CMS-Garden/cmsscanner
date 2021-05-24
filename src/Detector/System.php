<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector;

/**
 * Class System
 * @package Cmsgarden\Cmsscanner\Detector
 *
 * @since   1.0.0
 */
class System
{
    /**
     * @var string installation name
     */
    public $name = null;

    /**
     * @var string version number of this installation
     */
    public $version = null;

    /**
     * @var \SplFileInfo path of this installation
     */
    public $path = null;

    /**
     * @var array The list of the modules
     */
    public $modules = array();

    public function __construct($name, \SplFileInfo $path)
    {
        $this->setName($name);
        $this->setPath($path);
    }

    /**
     * get system name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set system name
     *
     * @param   string  $name  system name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * get installation path
     *
     * @return \SplFileInfo
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * set installation path
     *
     * @param   \SplFileInfo  $path  path
     */
    public function setPath(\SplFileInfo $path)
    {
        $this->path = $path;
    }

    /**
     * get installation version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * set installation version
     *
     * @param   string  $version  version number
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get the installed modules.
     *
     * @return Module[] The installed modules.
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Add a module.
     *
     * @param Module $module The module to add.
     *
     * @return void
     */
    public function addModule(Module $module)
    {
        $this->modules[] = $module;
    }
}
