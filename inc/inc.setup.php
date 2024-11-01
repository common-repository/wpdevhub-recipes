<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ben
 * Date: 5/19/15
 * Time: 9:23 PM
 * To change this template use File | Settings | File Templates.
 */

if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check

// Setup the Add Define Routine
function wpdevhub_drc_add_define($key, $val) {
    if (!defined($key)) {
        define($key, $val);
        return true;
    }
    return false;
}

/********** INCLUDES **********/
$classes = array();

// Core Files
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardCustomPostType.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardEditor.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardGroupRecord.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardLinkRecord.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardMain.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardManager.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardMetaBox.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardMetaBoxAndDbObject.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardMetaBoxObject.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardObjectRecord.php';
$classes[] = dirname(__FILE__).'/../classes/core/class.WPDEVHUB_DRC_StandardSetting.php';

// Utilities
$classes[] = dirname(__FILE__).'/../classes/class.WPDEVHUB_DRC_Utilities.php';
$classes[] = dirname(__FILE__).'/../classes/class.WPDEVHUB_DRC_Box.php';
$classes[] = dirname(__FILE__).'/../classes/class.WPDEVHUB_DRC_MessagePopup.php';

foreach($classes as $classpath){
    include($classpath);
}



// Base Constants that should be overridden
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_APP_CODE', 'undefined');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_SLUG', 'wpdevhub-'.WPDEVHUB_CONST_DRC_APP_CODE);
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_FOLDER', 'wpdevhub-'.WPDEVHUB_CONST_DRC_APP_CODE);
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_DB_PREFIX', 'wpdevhub-'.WPDEVHUB_CONST_DRC_APP_CODE);
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_SETTINGS_PREFIX', WPDEVHUB_CONST_DRC_SLUG.'-');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_URL', plugins_url() . "/" . WPDEVHUB_CONST_DRC_FOLDER);
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_DIR', WP_PLUGIN_DIR . '/' . WPDEVHUB_CONST_DRC_FOLDER);
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_URL_IMAGES', WPDEVHUB_CONST_DRC_URL . '/images');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PLUGIN_FILE', WPDEVHUB_CONST_DRC_DIR . '/index.php');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_DOTCOM_URL', 'https://www.wpdevhub.com');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_USE_UPDATER',false);      // Use the WordPress Updater by Default
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PROMO_DCD',true);       // Turn off DCD.


// Pages
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PAGE_HOME', 'home');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PAGE_ZONES', 'zones');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PAGE_SETTINGS', 'settings');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PAGE_REPORTS', 'reports');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PAGE_PREVIEW', 'preview');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_PAGE_SUPPORT', 'support');


// Zones
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_ZONE_GROUP_NAME', 'Zone');
wpdevhub_drc_add_define('WPDEVHUB_CONST_DRC_ZONE_ITEM_NAME', 'Item');


// Environment Specific Loading
include dirname(__FILE__).'/inc.ver.php';

