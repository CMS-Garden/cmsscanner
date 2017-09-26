<?php

/**
 * Contains support functions used by PivotX.
 *
 * @package pivotx
 */

// ---------------------------------------------------------------------------
//
// PIVOTX - LICENSE:
//
// This file is part of PivotX. PivotX and all its parts are licensed under
// the GPL version 2. see: http://docs.pivotx.net/doku.php?id=help_about_gpl
// for more information.
//
// $Id: lib.php 3511 2011-02-16 21:14:17Z pivotlog $
//
// ---------------------------------------------------------------------------

DEFINE('INPIVOTX', TRUE);

$version = "2.2.5";
$codename = "";
$svnrevision = '$Rev: 3511 $';

$minrequiredphp = "5.2.0";
$minrequiredmysql = "4.1";
$dbversion = "11"; // Used to track if it's necessary to upgrade the DB.

