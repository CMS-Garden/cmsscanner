<?php
/**
 * Central CONTENIDO file to initialize the application. Performs following steps:
 * - Initial PHP setting
 * - Does basic security check
 * - Includes configurations
 * - Runs validation of request variables
 * - Loads available login languages
 * - Initializes CEC
 * - Includes userdefined configuration
 * - Sets/Checks DB connection
 * - Initializes UriBuilder
 *
 * @TODO: Collect all startup (bootstrap) related jobs into this file...
 *
 * @package          Core
 * @subpackage       Backend
 * @version          SVN Revision $Rev:$
 *
 * @author           Unknown
 * @copyright        four for business AG <www.4fb.de>
 * @license          http://www.contenido.org/license/LIZENZ.txt
 * @link             http://www.4fb.de
 * @link             http://www.contenido.org
 */

defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

global $cfg, $cfgClient, $errsite_idcat, $errsite_idart;

/* Initial PHP error handling settings.
 * NOTE: They will be overwritten below...
 */
// Don't display errors
@ini_set('display_errors', false);

// Log errors to a file
@ini_set('log_errors', true);

// Report all errors except warnings
error_reporting(E_ALL ^E_NOTICE);


/* Initial PHP session settings.
 * NOTE: When you change these values by custom configuration, the length of the session ID may differ from 32 characters.
 * As this length was a criteria for session ID validity in previous versions of CONTENIDO, changes may affect your scripts.
 */

// Set session hash function to SHA-1
@ini_set('session.hash_function', 1);

// Set 5 bits per character
@ini_set('session.hash_bits_per_character', 5);

/*
 * Do not edit this value!
 *
 * If you want to set a different enviroment value please define it in your .htaccess file
 * or in the server configuration.
 *
 * SetEnv CON_ENVIRONMENT development
 */
if (!defined('CON_ENVIRONMENT')) {
    if (getenv('CONTENIDO_ENVIRONMENT')) {
        $sEnvironment = getenv('CONTENIDO_ENVIRONMENT');
    } elseif (getenv('CON_ENVIRONMENT')) {
        $sEnvironment = getenv('CON_ENVIRONMENT');
    } else {
        // @TODO: provide a possibility to set the environment value via file
        $sEnvironment = 'production';
    }

    define('CON_ENVIRONMENT', $sEnvironment);
}

/*
 * SetEnv CON_VERSION
 */
if (!defined('CON_VERSION')) {

    define('CON_VERSION', '4.9.1');

}

// (string) Path to folder containing all contenido configuration files
//          Use environment setting!
$cfg['path']['contenido_config'] = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../..')) . '/data/config/' . CON_ENVIRONMENT . '/';

// Temporary backend path, will be re-set again later...
$backendPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/..'));

include_once($backendPath . '/includes/functions.php54.php');

// Security check: Include security class and invoke basic request checks
require_once($backendPath . '/classes/class.registry.php');
require_once($backendPath . '/classes/class.security.php');
require_once($backendPath . '/classes/class.requestvalidator.php');
require_once($backendPath . '/classes/class.filehandler.php');
try {
    $requestValidator = cRequestValidator::getInstance();
    $requestValidator->checkParams();
} catch (cFileNotFoundException $e) {
    die($e->getMessage());
}

// "Workaround" for register_globals=off settings.
require_once($backendPath . '/includes/globals_off.inc.php');

// Check if configuration file exists, this is a basic indicator to find out, if CONTENIDO is installed
if (!cFileHandler::exists($cfg['path']['contenido_config'] . 'config.php')) {
    $msg = "<h1>Fatal Error</h1><br>"
        . "Could not open the configuration file <b>config.php</b>.<br><br>"
        . "Please make sure that you saved the file in the setup program."
        . "If you had to place the file manually on your webserver, make sure that it is placed in your contenido/data/config/{environment}/ directory.";
    die($msg);
}

// Include some basic configuration files
require_once($cfg['path']['contenido_config'] . 'config.php');
require_once($cfg['path']['contenido_config'] . 'config.path.php');
require_once($cfg['path']['contenido_config'] . 'config.misc.php');
require_once($cfg['path']['contenido_config'] . 'config.templates.php');
require_once($cfg['path']['contenido_config'] . 'cfg_sql.inc.php');

if (cFileHandler::exists($cfg['path']['contenido_config'] . 'config.clients.php')) {
    require_once($cfg['path']['contenido_config'] . 'config.clients.php');
    if (is_array($errsite_idcat)) {
        $errsite_idcat = array();
    }
    if (is_array($errsite_idart)) {
        $errsite_idart = array();
    }
    foreach ($cfgClient as $id => $aClientCfg) {
        if (is_array($aClientCfg)) {
            $errsite_idcat[$id] = $aClientCfg['errsite']['idcat'];
            $errsite_idart[$id] = $aClientCfg['errsite']['idart'];
        }
    }
}

// Include userdefined configuration (if available), where you are able to
// extend/overwrite core settings from included configuration files above
if (cFileHandler::exists($cfg['path']['contenido_config'] . 'config.local.php')) {
    require_once($cfg['path']['contenido_config'] . 'config.local.php');
}

// Takeover configured PHP settings
if ($cfg['php_settings'] && is_array($cfg['php_settings'])) {
    foreach ($cfg['php_settings'] as $settingName => $value) {
        // date.timezone is handled separately
        if ($settingName !== 'date.timezone') {
            @ini_set($settingName, $value);
        }
    }
}
error_reporting($cfg['php_error_reporting']);

// force date.timezone setting
$timezoneCfg = $cfg['php_settings']['date.timezone'];
if (!empty($timezoneCfg) && ini_get('date.timezone') !== $timezoneCfg) {
    // if the timezone setting from the cfg differs from the php.ini setting, set timezone from CFG
    date_default_timezone_set($timezoneCfg);
} else if (empty($timezoneCfg) && (ini_get('date.timezone') === '' || ini_get('date.timezone') === false)) {
    // if there are no timezone settings, set UTC timezone
    date_default_timezone_set('UTC');
}

$backendPath = cRegistry::getBackendPath();

// Various base API functions
require_once($backendPath . $cfg['path']['includes'] . 'api/functions.api.general.php');

// Initialization of autoloader
require_once($backendPath . $cfg['path']['classes'] . 'class.autoload.php');
cAutoload::initialize($cfg);

// Generate arrays for available login languages
// Author: Martin Horwath
$localePath = $cfg['path']['contenido_locale'];
if (is_dir($localePath)) {
    if ($handle = opendir($localePath)) {
        while (($locale = readdir($handle)) !== false) {
            if (is_dir($localePath . $locale) && $locale != '..' && $locale != '.') {
                if (cFileHandler::exists($localePath . $locale . '/LC_MESSAGES/contenido.po') &&
                    cFileHandler::exists($localePath . $locale . '/LC_MESSAGES/contenido.mo')) {
                    $cfg['login_languages'][] = $locale;
                    $cfg['lang'][$locale] = 'lang_' . $locale . '.xml';
                }
            }
        }
        closedir($handle);
    }
}

// Some general includes
cInclude('includes', 'functions.general.php');
cInclude('includes', 'functions.i18n.php');

// Initialization of CEC
$_cecRegistry = cApiCecRegistry::getInstance();
require_once($cfg['path']['contenido_config'] . 'config.chains.php');

// load all system chain inclusions if there are any
if (cFileHandler::exists($cfg['path']['contenido_config'] . 'config.chains.load.php')) {
    include_once($cfg['path']['contenido_config'] . 'config.chains.load.php');
}

// Set default database connection parameter
cDb::setDefaultConfiguration($cfg['db']);

// Initialize UriBuilder, configuration is set in data/config/{environment}/config.misc.php
cUriBuilderConfig::setConfig($cfg['url_builder']);

unset($backendPath, $localePath, $timezoneCfg, $handle);