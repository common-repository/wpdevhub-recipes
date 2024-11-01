<?php
/*
 * Plugin Name:   WPDevHub Recipes
 * Version:       2.7
 * Plugin URI:    https://www.wpdevhub.com/wordpress-plugins/recipe-catalog/
 * Description:   Create an easy to use Catalog of Recipes for your website or blog
 * Author:        WPDevHub
 * Author URI:    https://www.wpdevhub.com/
 */

define('WPDEVHUB_CONST_DRC_PLUGIN_TITLE', 'WPDevHub Recipes');
define('WPDEVHUB_CONST_DRC_APP_CODE', 'drc');
define('WPDEVHUB_CONST_DRC_FOLDER', 'wpdevhub-recipes');

// Standard Setup Steps
include dirname(__FILE__).'/inc/inc.setup.php';

// Additional Components to Setup

// Class Includes
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_Main.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_Recipe.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_ExtraInfo.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_ExtraInfoMetaBox.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_Ingredient.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_IngredientMetaBox.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_Instruction.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_InstructionMetaBox.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_Media.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_MediaMetaBox.php';
include dirname(__FILE__).'/classes/class.WPDEVHUB_DRC_Recipes_RecipeShortCodeMetaBox.php';

// Actions
add_action( 'init', array('WPDEVHUB_DRC_Recipes_Main', 'wpActionInit'), 0 );

// Activations Hooks
register_activation_hook(__FILE__, array('WPDEVHUB_DRC_Recipes_Main', 'wpActionActivate'));

