<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/21/19
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_RecipeShortCodeMetaBox extends WPDEVHUB_DRC_StandardMetaBox{

    const KEYNAME = "wpdevhub_drc_shortcode";
    const TITLE = "Recipe Shortcode";
    const SCREEN = WPDEVHUB_DRC_Recipes_Recipe::KEYNAME;

    const CONTEXT = "side";       // Either "normal", "advanced" or "side"
    const PRIORITY = "default";          // Either "default", "core", "high" or "low"

    public static function renderCustom( $post ){

        // Display the shortcode

        $html = '';

        $shortcode = WPDEVHUB_DRC_Recipes_Recipe::getInsertRecipeShortcode($post->ID);

        $html = '<input type="text" style="width:100%; padding:5px;" value=\''.$shortcode.'\' onclick="this.select();" />';

        echo $html;

    }

    public static function saveCustom( $post_id ){

        // Nothing to do here

    }

}
