<?php
/**
 * @package    CMSScanner
 * @copyright  Copyright (C) 2014 - 2021 CMS-Garden.org
 * @license    MIT <https://tldrlegal.com/license/mit-license>
 * @link       https://www.cms-garden.org
 */

namespace Cmsgarden\Cmsscanner\Detector\Adapter;

use Cmsgarden\Cmsscanner\Detector\System;
use Cmsgarden\Cmsscanner\Detector\Module;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class JoomlaAdapter
 * @package Cmsgarden\Cmsscanner\Detector\Adapter
 *
 * @since   1.0.0
 */
class JoomlaAdapter implements AdapterInterface
{
    /**
     * Joomla has changed the way how the version number is stored multiple times, so we need this comprehensive array
     * @var array
     */
    private $version = array(
            "files" => array(
                "/includes/version.php",
                "/libraries/joomla/version.php",
                "/libraries/cms/version/version.php",
                "/libraries/src/Version.php",
            ),
            "regex_release" => "/\\\$?RELEASE\s*=\s*'([\d.]+)';/",
            "regex_devlevel" => "/\\\$?DEV_LEVEL\s*=\s*'([^']+)';/",
            "regex_major" => "/\\\$?MAJOR_VERSION\s*=\s*([\d.]+);/",
            "regex_minor" => "/\\\$?MINOR_VERSION\s*=\s*([\d.]+);/",
            "regex_patch" => "/\\\$?PATCH_VERSION\s*=\s*([\d.]+);/",
        );


    private $componentPaths = array(
        'components',
        'administrator/components'
    );

    private $modulePaths = array(
        'modules',
        'administrator/modules'
    );

    private $pluginPath = 'plugins';

    private $templatePaths = array(
        'templates',
        'administrator/templates'
    );

    protected $coreExtensions = array(
        // Components
        // Frontend
        'com_ajax',
        'com_banners',
        'com_config',
        'com_contact',
        'com_content',
        'com_contenthistory',
        'com_csp',
        'com_fields',
        'com_finder',
        'com_media',
        'com_modules',
        'com_newsfeeds',
        'com_privacy',
        'com_tags',
        'com_users',
        'com_wrapper',
        // Backend only
        'com_actionlogs',
        'com_admin',
        'com_associations',
        'com_cache',
        'com_categories',
        'com_checkin',
        'com_cpanel',
        'com_installer',
        'com_joomlaupdate',
        'com_languages',
        'com_login',
        'com_mails',
        'com_menus',
        'com_messages',
        'com_plugins',
        'com_postinstall',
        'com_redirect',
        'com_templates',
        'com_workflow',
        // 3.x only
        'com_mailto',
        'com_search',
        // Modules
        // Frontend
        'mod_articles_archive',
        'mod_articles_categories',
        'mod_articles_category',
        'mod_articles_latest',
        'mod_articles_news',
        'mod_articles_popular',
        'mod_banners',
        'mod_breadcrumbs',
        'mod_custom',
        'mod_feed',
        'mod_finder',
        'mod_footer',
        'mod_languages',
        'mod_login',
        'mod_menu',
        'mod_random_image',
        'mod_related_items',
        'mod_stats',
        'mod_syndicate',
        'mod_tags_popular',
        'mod_tags_similar',
        'mod_users_latest',
        'mod_whosonline',
        'mod_wrapper',
        // Backend only
        'mod_frontend',
        'mod_latest',
        'mod_latestactions',
        'mod_logged',
        'mod_loginsupport',
        'mod_multilangstatus',
        'mod_popular',
        'mod_post_installation_messages',
        'mod_privacy_dashboard',
        'mod_privacy_status',
        'mod_quickicon',
        'mod_sampledata',
        'mod_stats_admin',
        'mod_submenu',
        'mod_title',
        'mod_toolbar',
        'mod_version',
        // 3.x only
        'mod_search',
        'mod_status',
        // Plugins
        // actionlog
        'plg_actionlog_joomla',
        // api-authentication
        'plg_api-authentication_basic',
        'plg_api-authentication_token',
        // authentication
        'plg_authentication_cookie',
        'plg_authentication_joomla',
        'plg_authentication_ldap',
        // Behaviour
        'plg_behaviour_taggable',
        'plg_behaviour_versionable',
        // captcha
        'plg_captcha_recaptcha',
        'plg_captcha_recaptcha_invisible',
        // content
        'plg_content_confirmconsent',
        'plg_content_contact',
        'plg_content_emailcloak',
        'plg_content_fields',
        'plg_content_finder',
        'plg_content_imagelazyload',
        'plg_content_joomla',
        'plg_content_loadmodule',
        'plg_content_pagebreak',
        'plg_content_pagenavigation',
        'plg_content_vote',
        // editors
        'plg_editors_codemirror',
        'plg_editors_none',
        'plg_editors_tinymce',
        // editors-xtd
        'plg_editors-xtd_article',
        'plg_editors-xtd_contact',
        'plg_editors-xtd_fields',
        'plg_editors-xtd_image',
        'plg_editors-xtd_menu',
        'plg_editors-xtd_module',
        'plg_editors-xtd_pagebreak',
        'plg_editors-xtd_readmore',
        // extension
        'plg_extension_finder',
        'plg_extension_joomla',
        'plg_extension_namespacemap',
        // fields
        'plg_fields_calendar',
        'plg_fields_checkboxes',
        'plg_fields_color',
        'plg_fields_editor',
        'plg_fields_imagelist',
        'plg_fields_integer',
        'plg_fields_list',
        'plg_fields_media',
        'plg_fields_radio',
        'plg_fields_sql',
        'plg_fields_subfields',
        'plg_fields_text',
        'plg_fields_textarea',
        'plg_fields_url',
        'plg_fields_user',
        'plg_fields_usergrouplist',
        // Filesystem
        'plg_filesystem_local',
        // finder
        'plg_finder_categories',
        'plg_finder_contacts',
        'plg_finder_content',
        'plg_finder_newsfeeds',
        'plg_finder_tags',
        // installer
        'plg_installer_folderinstaller',
        'plg_installer_override',
        'plg_installer_packageinstaller',
        'plg_installer_urlinstaller',
        'plg_installer_webinstaller',
        // media-action
        'plg_media-action_crop',
        'plg_media-action_resize',
        'plg_media-action_rotate',
         // privacy
        'plg_privacy_actionlogs',
        'plg_privacy_consents',
        'plg_privacy_contact',
        'plg_privacy_content',
        'plg_privacy_message',
        'plg_privacy_user',
        // quickicon
        'plg_quickicon_downloadkey',
        'plg_quickicon_extensionupdate',
        'plg_quickicon_joomlaupdate',
        'plg_quickicon_overridecheck',
        'plg_quickicon_phpversioncheck',
        'plg_quickicon_privacycheck',
        // sampledata
        'plg_sampledata_blog',
        'plg_sampledata_multilang',
        'plg_sampledata_testing',
        // system
        'plg_system_accessibility',
        'plg_system_actionlogs',
        'plg_system_cache',
        'plg_system_debug',
        'plg_system_fields',
        'plg_system_highlight',
        'plg_system_httpheaders',
        'plg_system_languagecode',
        'plg_system_languagefilter',
        'plg_system_log',
        'plg_system_logout',
        'plg_system_logrotation',
        'plg_system_privacyconsent',
        'plg_system_redirect',
        'plg_system_remember',
        'plg_system_sef',
        'plg_system_sessiongc',
        'plg_system_skipto',
        'plg_system_stats',
        'plg_system_updatenotification',
        'plg_system_webauthn',
        // twofactorauth
        'plg_twofactorauth_totp',
        'plg_twofactorauth_yubikey',
        // user
        'plg_user_contactcreator',
        'plg_user_joomla',
        'plg_user_profile',
        'plg_user_terms',
        'plg_user_token',
        // webservices
        'plg_webservices_banners',
        'plg_webservices_config',
        'plg_webservices_contact',
        'plg_webservices_content',
        'plg_webservices_installer',
        'plg_webservices_languages',
        'plg_webservices_menus',
        'plg_webservices_messages',
        'plg_webservices_modules',
        'plg_webservices_newsfeeds',
        'plg_webservices_plugins',
        'plg_webservices_privacy',
        'plg_webservices_redirect',
        'plg_webservices_tags',
        'plg_webservices_templates',
        'plg_webservices_users',
        // workflow
        'plg_workflow_featuring',
        'plg_workflow_notification',
        'plg_workflow_publishing',
        // 3.x and older only
        'plg_authentication_gmail',
        'plg_content_geshi',
        'plg_search_categories',
        'plg_search_contacts',
        'plg_search_content',
        'plg_search_newsfeeds',
        'plg_search_tags',
        'plg_system_p3p',
        // Templates
        // 1.5
        // Frontend
        'beez',
        'ja_purity',
        'rhuk_milkyway',
        // Backend
        'khepri',
        // 2.5
        // Frontend
        'atomic',
        'beez_20',
        'beez5',
        // Backend
        'bluestork',
        'hathor',
        // 3.x
        // Frontend
        'beez3',
        'protostar',
        // Backend
        'hathor',
        'isis',
        // 4.0
        // Frontend
        'cassiopeia',
        // Backend
        'atun',
    );

    /**
     * Joomla has a file called configuration.php that can be used to search for working installations
     *
     * @param   Finder  $finder  finder instance to append the criteria
     *
     * @return  Finder
     */
    public function appendDetectionCriteria(Finder $finder)
    {
        $finder->name('configuration.php');

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
        if ($file->getFilename() != "configuration.php") {
            return false;
        }

        if (stripos($file->getContents(), "JConfig") === false
            && stripos($file->getContents(), 'mosConfig') === false) {
            return false;
        }

        // False positive "Akeeba Backup Installer"
        if (stripos($file->getContents(), "class ABIConfiguration") !== false) {
            return false;
        }

        // False positive mock file in unit test folder
        if (stripos($file->getContents(), "Joomla.UnitTest") !== false) {
            return false;
        }

        // False positive mock file in unit test folder
        if (stripos($file->getContents(), "Joomla\Framework\Test") !== false) {
            return false;
        }

        $path = new \SplFileInfo($file->getPath());

        // Return result if working
        return new System($this->getName(), $path);
    }

    /**
     * determine version of a Joomla installation within a specified path
     *
     * @param   \SplFileInfo  $path  directory where the system is installed
     *
     * @return  null|string
     */
    public function detectVersion(\SplFileInfo $path)
    {
        // Iterate through version files
        foreach ($this->version['files'] as $file) {
            $versionFile = $path->getRealPath() . $file;

            if (!file_exists($versionFile)) {
                continue;
            }

            if (!is_readable($versionFile)) {
                continue; // @codeCoverageIgnore
            }

            preg_match($this->version['regex_major'], file_get_contents($versionFile), $major);
            preg_match($this->version['regex_minor'], file_get_contents($versionFile), $minor);
            preg_match($this->version['regex_patch'], file_get_contents($versionFile), $patch);

            if (count($major) && count($minor) && count($patch)) {
                return $major[1] . '.' . $minor[1] . '.' . $patch[1];
            }

            if (count($major) && count($minor)) {
                return $major[1] . '.' . $minor[1] . 'x';
            }

            if (count($major)) {
                return $major[1] . '.x.x';
            }

            // Legacy handling for all version < 3.8.0
            preg_match($this->version['regex_release'], file_get_contents($versionFile), $release);
            preg_match($this->version['regex_devlevel'], file_get_contents($versionFile), $devlevel);

            if (count($release) && count($devlevel)) {
                return $release[1] . '.' . $devlevel[1];
            }

            if (count($release)) {
                return $release[1] . '.x';
            }

            // We can not detect any version
            continue;
        }

        return null;
    }

    /**
     * @InheritDoc
     */
    public function detectModules(\SplFileInfo $path)
    {
        $modules = array();

        foreach ($this->modulePaths as $mpath) {
            foreach (glob(sprintf('%s/%s/*', $path->getRealPath(), $mpath), GLOB_ONLYDIR) as $dir) {
                $infoFile = sprintf('%s/%s.xml', $dir, pathinfo($dir, PATHINFO_FILENAME));

                if (file_exists($infoFile)) {
                    $info = $this->parseXMLInfoFile($infoFile);
                    $modules[] = new Module($info['name'], $dir, $info['version'], 'module');
                }
            }
        }

        $this->detectComponents($path, $modules);
        $this->detectPlugins($path, $modules);
        $this->detectTemplates($path, $modules);

        // Remove the Core Extensions form the return array
        foreach ($modules as $key => $module) {
            $moduleName = strtolower($module->name);

            if (in_array($moduleName, $this->coreExtensions)) {
                unset($modules[$key]);
            }
        }

        return array_values($modules);
    }

    /**
     * detects installed joomla components
     *
     * @param \SplFileInfo $path
     * @param array        $modules
     */
    private function detectComponents(\SplFileInfo $path, array &$modules)
    {
        foreach ($this->componentPaths as $cpath) {
            foreach (glob(sprintf('%s/%s/*', $path->getRealPath(), $cpath), GLOB_ONLYDIR) as $dir) {
                $filename = pathinfo($dir, PATHINFO_FILENAME);
                $filename = substr($filename, strpos($filename, '_') + 1);
                $infoFile = sprintf('%s/%s.xml', $dir, $filename);

                if (file_exists($infoFile)) {
                    $info = $this->parseXMLInfoFile($infoFile);
                    $modules[] = new Module($info['name'], $dir, $info['version'], 'component');
                }
            }
        }
    }

    /**
     * detects installed joomla plugins
     *
     * @param \SplFileInfo $path
     * @param array        $modules
     */
    private function detectPlugins(\SplFileInfo $path, array &$modules)
    {
        $foundPlugin = false;

        // search for plugins in Joomla > 1.5 first
        foreach (glob(sprintf('%s/%s/*/*', $path->getRealPath(), $this->pluginPath), GLOB_ONLYDIR) as $dir) {
            $infoFile = sprintf('%s/%s.xml', $dir, pathinfo($dir, PATHINFO_FILENAME));

            if (file_exists($infoFile)) {
                $info = $this->parseXMLInfoFile($infoFile);
                $modules[] = new Module($info['name'], $dir, $info['version'], 'plugin');

                $foundPlugin = true;
            }
        }

        // skip legacy plugin search if first step had been succesful
        if ($foundPlugin) {
            return;
        }

        // search for plugins in Joomla 1.5
        foreach (glob(sprintf('%s/%s/*/*.xml', $path->getRealPath(), $this->pluginPath)) as $infoFile) {
            if (file_exists($infoFile)) {
                $info = $this->parseXMLInfoFile($infoFile);
                $modules[] = new Module($info['name'], dirname($infoFile), $info['version'], 'plugin');
            }
        }
    }

    /**
     * detects installed joomla templates
     *
     * @param \SplFileInfo $path
     * @param array        $modules
     */
    private function detectTemplates(\SplFileInfo $path, array &$modules)
    {
        foreach ($this->templatePaths as $tpath) {
            foreach (glob(sprintf('%s/%s/*', $path->getRealPath(), $tpath), GLOB_ONLYDIR) as $dir) {
                $infoFile = sprintf('%s/templateDetails.xml', $dir);

                if (file_exists($infoFile)) {
                    $info = $this->parseXMLInfoFile($infoFile);
                    $modules[] = new Module($info['name'], $dir, $info['version'], 'template');
                }
            }
        }
    }

    /**
     * Parse an XML info file.
     *
     * @param string $file Full file path of the xml info file.
     *
     * @return array The data of the XML file.
     */
    private function parseXMLInfoFile($file)
    {
        $name = null;
        $version = null;
        $content = file_get_contents($file);

        if (preg_match('/<name>(.*)<\/name>/', $content, $matches)) {
            $name = $matches[1];
        }

        if (preg_match('/<version>(.*)<\/version>/', $content, $matches)) {
            $version = $matches[1];
        }

        return array('name' => $name, 'version' => $version);
    }

    /***
     * @return string
     */
    public function getName()
    {
        return 'Joomla';
    }
}
