<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 CMS-Garden.org
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.cms-garden.org
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
        return new System("TestSystem", new SplFileInfo(__FILE__));
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
