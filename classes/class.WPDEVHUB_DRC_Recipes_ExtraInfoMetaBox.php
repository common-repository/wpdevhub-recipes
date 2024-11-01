<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/21/17
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_ExtraInfoMetaBox extends WPDEVHUB_DRC_StandardMetaBox{

    const KEYNAME = "wpdevhub_drc_extrainfo";
    const TITLE = "Recipe Extra Info";
    const SCREEN = WPDEVHUB_DRC_Recipes_Recipe::KEYNAME;
    const CONTEXT = "side";

    public static function renderCustom( $post ){
        // Use get_post_meta to retrieve an existing value from the database.
        $extraInfo = self::getPostMeta($post->ID);

        $cooktimeValue = 0;
        $cooktimeMeasurement = WPDEVHUB_DRC_Recipes_ExtraInfo::UNIT_DEFAULT;
        $difficulty = WPDEVHUB_DRC_Recipes_ExtraInfo::DIFFICULTY_DEFAULT;
        if(!empty($extraInfo) && is_a($extraInfo, "WPDEVHUB_DRC_Recipes_ExtraInfo")){
            $cooktimeValue = $extraInfo->cooktimeValue;
            $cooktimeMeasurement = $extraInfo->cooktimeMeasurement;
            $difficulty = $extraInfo->difficulty;
        }

        $html = '';
        $html .= 'Cooktime: <input type="text" id="WPDEVHUB_DRC_extrainfo_value" name="WPDEVHUB_DRC_extrainfo_value" size="10" value="'.$cooktimeValue.'">';
        $html .= '<select id="WPDEVHUB_DRC_extrainfo_measurement" name="WPDEVHUB_DRC_extrainfo_measurement">';
        $measurements = WPDEVHUB_DRC_Recipes_ExtraInfo::getAllMeasurementMarks();
        foreach($measurements as $key=>$value){
            $selected='';
            if($key == $cooktimeMeasurement){
                $selected = ' selected="selected"';
            }
            $html .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';

        }
        $html .= '</select>';

        $html .= '<br />';

        $difficulties = WPDEVHUB_DRC_Recipes_ExtraInfo::getAllDifficultyMarks();
        $html .= 'Difficulty: <select id="WPDEVHUB_DRC_extrainfo_difficulty" name="WPDEVHUB_DRC_extrainfo_difficulty">';
        foreach($difficulties as $key=>$value){
            $selected='';
            if($difficulty == $key){
                $selected = ' selected="selected"';
            }
            $html .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
        }
        $html .= '</select>';

        echo $html;
    }

    public static function saveCustom( $post_id ){

        /* notes regarding sanitizing:
            https://developer.wordpress.org/plugins/security/securing-input/
            https://wordpress.stackexchange.com/questions/168315/sanitizing-integer-input-for-update-post-meta
        */

        $cooktimeValue = 0;
        if(array_key_exists("WPDEVHUB_DRC_extrainfo_value", $_POST)){
            // Sanitize the incoming value -- intval will default to 0 if bad input
            $cooktimeValue = intval($_POST["WPDEVHUB_DRC_extrainfo_value"]);
        }

        $cooktimeMeasurement = WPDEVHUB_DRC_Recipes_ExtraInfo::UNIT_DEFAULT;
        if(array_key_exists("WPDEVHUB_DRC_extrainfo_measurement", $_POST)){
            // Sanitize the incoming value -- intval will default to 0 if bad input
            $cooktimeMeasurement = intval($_POST["WPDEVHUB_DRC_extrainfo_measurement"]);
        }

        $difficulty = WPDEVHUB_DRC_Recipes_ExtraInfo::DIFFICULTY_DEFAULT;
        if(array_key_exists("WPDEVHUB_DRC_extrainfo_difficulty", $_POST)){
            // Sanitize the incoming value -- intval will default to 0 if bad input
            $difficulty = intval($_POST["WPDEVHUB_DRC_extrainfo_difficulty"]);
        }

        // Create the cooktime object
        $extraInfo = new WPDEVHUB_DRC_Recipes_ExtraInfo();
        $extraInfo->cooktimeValue = $cooktimeValue;
        $extraInfo->cooktimeMeasurement = $cooktimeMeasurement;
        $extraInfo->difficulty = $difficulty;

        // Save the object into the meta data
        self::savePostMeta($post_id, $extraInfo);

    }

}
