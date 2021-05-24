<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Tests\Stubs;

use Cmsgarden\Cmsscanner\Detector\System;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Cmsgarden\Cmsscanner\Detector\Adapter\AdapterInterface;

class TestAdapter implements AdapterInterface
{

    public function appendDetectionCriteria(Finder $finder)
    {
        return $finder;
    }

    public function detectSystem(SplFileInfo $file)
    {
        return new System("TestSystem", new \SplFileInfo(__FILE__));
    }

    public function detectVersion(\SplFileInfo $path)
    {
        return null;
    }

    public function detectModules(\SplFileInfo $path)
    {
        // TODO: Implement detectModules() method.
    }

    public function getName()
    {
        return 'Test';
    }
}
