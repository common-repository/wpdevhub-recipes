<?php
// Set the class name
$settingsClassname = 'WPDEVHUB_DRC_Recipes_Main';

// Check to see if we need to flush the rewrite rules
$needsFlush = WPDEVHUB_DRC_StandardSetting::getSetting("flush_rewrite_rules");
if(!empty($needsFlush) && $needsFlush==1){
    WPDEVHUB_DRC_StandardMain::flushRewriteRules();
    WPDEVHUB_DRC_StandardSetting::saveSetting("flush_rewrite_rules", 0);
}

// Get the options
$options = call_user_func(array($settingsClassname, 'buildSettingsEditorOptions'));

// Get the Settings Object before going to the main page
$preObject = WPDEVHUB_DRC_StandardSetting::getSettingsObject($options);

// Include the base settings class
include("inc.settings-base.php");

// Get the Settings Objct after doing the main page
$postObject = WPDEVHUB_DRC_StandardSetting::getSettingsObject($options);

// Evaluate the Pages that may need the rewrite rules
$flush = false;
if(WPDEVHUB_DRC_StandardSetting::compareSettingbyName($preObject, $postObject, WPDEVHUB_DRC_Recipes_Main::SETTINGS_VP_ENABLED)){
    $flush = true;
}
if(WPDEVHUB_DRC_StandardSetting::compareSettingbyName($preObject, $postObject, WPDEVHUB_DRC_Recipes_Main::SETTINGS_CATEGORIES_ENABLED)){
    $flush = true;
}
if(WPDEVHUB_DRC_StandardSetting::compareSettingbyName($preObject, $postObject, WPDEVHUB_DRC_Recipes_Main::SETTINGS_TAGS_ENABLED)){
    $flush = true;
}
if($flush){
    WPDEVHUB_DRC_StandardSetting::saveSetting("flush_rewrite_rules", 1);
}
