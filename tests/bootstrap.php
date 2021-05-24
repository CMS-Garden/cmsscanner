<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

// Maximise error reporting.

error_reporting(E_ALL & ~(E_STRICT|E_USER_DEPRECATED));
ini_set('display_errors', 1);

define('CMSSCANNER_MOCKFILES_PATH', __DIR__ . "/mockfiles");

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/stubs/TestAdapter.php';
