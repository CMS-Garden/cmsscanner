<?php
/**
 * @version		$Id: version.php 22381 2011-11-14 01:18:53Z dextercowley $
 * @package	Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('JPATH_BASE') or die();
/**
 * Version information
 *
 * @package	Joomla.Framework
 * @since	1.0
 */
class JVersion
{
    /** @var string Product */
    var $PRODUCT 	= 'Joomla!';
    /** @var int Main Release Level */
    var $RELEASE 	= '1.5';
    /** @var string Development Status */
    var $DEV_STATUS = 'Stable';
    /** @var int Sub Release Level */
    var $DEV_LEVEL 	= '25';
    /** @var int build Number */
    var $BUILD	= '';
    /** @var string Codename */
    var $CODENAME 	= 'senu takaa ama mamni';
    /** @var string Date */
    var $RELDATE 	= '14-November-2011';
    /** @var string Time */
    var $RELTIME 	= '18:00';
    /** @var string Timezone */
    var $RELTZ 	= 'GMT';
    /** @var string Copyright Text */
    var $COPYRIGHT 	= 'Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.';
    /** @var string URL */
    var $URL 	= '<a href="http://www.joomla.org">Joomla!</a> is Free Software released under the GNU General Public License.';

    /**
     *
     *
     * @return string Long format version
     */
    function getLongVersion()
    {
        return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL .' '
        . $this->DEV_STATUS
        .' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
        . $this->RELTIME .' '. $this->RELTZ;
    }

    /**
     *
     *
     * @return string Short version format
     */
    function getShortVersion() {
        return $this->RELEASE .'.'. $this->DEV_LEVEL;
    }

    /**
     *
     *
     * @return string Version suffix for help files
     */
    function getHelpVersion()
    {
        if ($this->RELEASE > '1.0') {
            return '.' . str_replace( '.', '', $this->RELEASE );
        } else {
            return '';
        }
    }

    /**
     * Compares two "A PHP standardized" version number against the current Joomla! version
     *
     * @return boolean
     * @see http://www.php.net/version_compare
     */
    function isCompatible ( $minimum ) {
        return (version_compare( JVERSION, $minimum, 'eq' ) == 1);
    }
}