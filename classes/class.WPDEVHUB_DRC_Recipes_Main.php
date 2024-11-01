<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ben
 * Date: 3/12/15
 * Time: 11:26 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_Main extends WPDEVHUB_DRC_StandardMain{

    static $classname = "WPDEVHUB_DRC_Recipes_Main";

    const CURRENT_VERSION = 2;

    const PAGE_HOME = "home";
    const PAGE_SETTINGS = "settings";

    const SC_ID_HOMEPAGE = 1;
    const SC_ID_DISPLAY_RECIPE = 2;
    const SC_ID_DISPLAY_CATEGORY = 3;

    
    const SETTINGS_PLUGIN_ENABLED = "plugin_enabled";

    const SETTINGS_VP_ENABLED = "vp_enabled";
    const SETTINGS_SLUG_BASE = "vp_slug_base";
    const SLUG_BASE = "recipes";
    const SETTINGS_CATEGORIES_ENABLED = "categories_enabled";
    const SETTINGS_TAGS_ENABLED = "tags_enabled";

    const SETTINGS_OPEN_BY_DEFAULT = "open_by_default";
    const SETTINGS_DISPLAY_INGREDIENTS = "display_ingredients";
    const SETTINGS_DISPLAY_DIFFICULTY_ALT = "display_difficulty_alt";
    const SETTINGS_DISPLAY_TIME = "display_time";
    const SETTINGS_DISPLAY_INSTRUCTIONS = "display_instructions";
    const SETTINGS_DISPLAY_MEDIA = "display_instructions";
    const SETTINGS_MEDIA_SHOW_CAPTIONS = "media_show_captions";
    const SETTINGS_DISPLAY_INDEX_RETURN_TOP = "index_return_top";
    const SETTINGS_DISPLAY_INDEX_RETURN_BOTTOM = "index_return_bottom";

    /*
    * WordPress Action Hook for init :: Generally the first action available to the Plugins
    */
    public static function wpActionInit(){
        parent::wpActionInit();

        // Add CSS and JS Resources
        self::addResourceToEnqueue(WPDEVHUB_CONST_DRC_SLUG.'-public-css', WPDEVHUB_CONST_DRC_URL.'/css/wpdevhub-drc.css', self::AMT_PUBLIC);
        wp_enqueue_script('jquery');

        // Add AJAX Handlers
        //self::addAjaxMapping($name, $callable, $type);

        // Add CRON Hooks
        //self::addCronHandler($name, $callable, $schedule);

        // Add Widgets
        //self::addWidget($classname);

        // Add Custom post types
        self::addCustomPostType( WPDEVHUB_DRC_Recipes_Recipe::KEYNAME , 'WPDEVHUB_DRC_Recipes_Recipe' );

        // Add MetaBox
        self::addMetaBox( 'WPDEVHUB_DRC_Recipes_MediaMetaBox' );
        self::addMetaBox( 'WPDEVHUB_DRC_Recipes_InstructionMetaBox' );
        self::addMetaBox( 'WPDEVHUB_DRC_Recipes_IngredientMetaBox' );
        self::addMetaBox( 'WPDEVHUB_DRC_Recipes_ExtraInfoMetaBox' );
        self::addMetaBox( 'WPDEVHUB_DRC_Recipes_RecipeShortCodeMetaBox' );

        // Add ShortCode
        self::addShortcode(self::SC_ID_DISPLAY_RECIPE, array('WPDEVHUB_DRC_Recipes_Recipe','shortcodeHandlerDisplayRecipe'));

        // Random Filters, etc
        add_filter( 'post_thumbnail_html', array( 'WPDEVHUB_DRC_Recipes_Recipe' , 'wpFilterPostThumbnailHtml' ), 20, 5 );

        // Remove the "View Link" if Virtual Pages are disabled
        add_filter( 'post_row_actions', array('WPDEVHUB_DRC_Recipes_Main','wpPostRowActions'), 10, 1 );

    }

    public static function wpActionActivate(){

        // Need to register the Taxonomies BEFORE calling the Flush Rewrite Rules Routine
        WPDEVHUB_DRC_Recipes_Recipe::registerTaxonomies();

        parent::wpActionActivate();
    }

    public static function enqueueAdminResources(){
        $dimbalVars = array(
            'measurements' => WPDEVHUB_DRC_Recipes_Ingredient::getAllMeasurementMarks(),
        );
        wp_localize_script( WPDEVHUB_CONST_DRC_SLUG.'-admin-js', 'WPDEVHUB_DRC_extra_vars', $dimbalVars );

    }

    public static function wpActionAdminMenu(){
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        //add_menu_page( 'Dimbal Troubleshooting Guides', WPDEVHUB_CONST_DRC_PLUGIN_TITLE, 'manage_options', WPDEVHUB_DRC_Utilities::buildPageSlug(self::PAGE_HOME), array('WPDEVHUB_DRC_Utilities','renderPage') );

        $dscf = strtolower('WPDEVHUB_DRC_');

        //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
        add_submenu_page( 'edit.php?post_type='.$dscf.'recipe', 'Settings', 'Settings', 'manage_options', WPDEVHUB_DRC_Utilities::buildPageSlug(self::PAGE_SETTINGS), array('WPDEVHUB_DRC_Utilities','renderPage'));

        //add_submenu_page( 'fake-slug-does-not-exist', 'Preview', 'Preview', 'manage_options', WPDEVHUB_DRC_Utilities::buildPageSlug(self::PAGE_PREVIEW), array('WPDEVHUB_DRC_Utilities','renderPage'));
    }

    public static function wpPostRowActions( $actions ){
        $arePageRewritesEnabled = WPDEVHUB_DRC_Recipes_Main::areVirtualPagesEnabled();
        if(empty($arePageRewritesEnabled)){
            // Virtual pages are not enabled -- remove the view link
            if( get_post_type() === WPDEVHUB_DRC_Recipes_Recipe::KEYNAME ){
                unset( $actions['view'] );
            }
        }
        return $actions;
    }

    public static function buildSettingsEditorOptions($object=null){

        $options[]=array(
            'rowType'=>'SectionHeader',
            'title'=>'Global Framework Settings',
        );

        $options[]=array(
            'title'=>'Plugin Enabled',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>'plugin_enabled',
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->plugin_enabled))?$object->plugin_enabled:true,
            'help'=>'True to enable the Plugin, False to disable it without uninstalling it.  If False, will prevent the display of all user facing recipes, etc...  Use this feature to disable the plugin globally without having to uninstall it.'
        );
        $options[]=array(
            'rowType'=>'SectionHeader',
            'title'=>'Virtual Page Settings',
        );
        $keyname = self::SETTINGS_VP_ENABLED;
        $options[]=array(
            'title'=>'Use Virtual Pages',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:TRUE,
            'help'=>'If Checked, virtual pages will be enabled allowing 1 single page per Recipe.  If Unchecked, Recipes can only be accessed via ShortCode.',
            //'callback'=>array('WPDEVHUB_DRC_StandardMain', 'flushRewriteRules')
        );
        $keyname = self::SETTINGS_SLUG_BASE;
        $options[]=array(
            'title'=>'Virtual Path',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_STRING,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_TEXT,
            'size'=>50,
            'value'=>(isset($object->$keyname))?$object->$keyname:self::SLUG_BASE,
            'help'=>'The virtual path off of '.site_url().' to house the recipes.  Default Value: "'.self::SLUG_BASE.'"',
        );
        $keyname = self::SETTINGS_CATEGORIES_ENABLED;
        $options[]=array(
            'title'=>'Use Virtual Categories',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:TRUE,
            'help'=>'If Checked, virtual categories will be supported if virtual pages are supported.',
        );
        $keyname = self::SETTINGS_TAGS_ENABLED;
        $options[]=array(
            'title'=>'Use Virtual Tags',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:TRUE,
            'help'=>'If Checked, virtual tags will be supported if virtual pages are supported.',
        );
        $options[]=array(
            'rowType'=>'SectionHeader',
            'title'=>'General Display Settings',
        );
        $keyname = self::SETTINGS_DISPLAY_INDEX_RETURN_TOP;
        $options[]=array(
            'title'=>'Display Top Return Link',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:false,
            'help'=>'If checked, will display a link at the top of the recipe to return to the index page.'
        );
        $keyname = self::SETTINGS_DISPLAY_INDEX_RETURN_BOTTOM;
        $options[]=array(
            'title'=>'Display Bottom Return Link',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'If checked, will display a link at the bottom of the recipe to return to the index page.'
        );
        $options[]=array(
            'rowType'=>'SectionHeader',
            'title'=>'Recipe Display Settings',
        );
        $keyname = self::SETTINGS_DISPLAY_INGREDIENTS;
        $options[]=array(
            'title'=>'Display Ingredient List',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'If checked, will display the ingredient section and any entered data on the recipe.'
        );
        $keyname = self::SETTINGS_DISPLAY_DIFFICULTY_ALT;
        $options[]=array(
            'title'=>'Display Difficulty Rating',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'If checked, will display the difficulty rating on the recipe.'
        );
        $keyname = self::SETTINGS_DISPLAY_TIME;
        $options[]=array(
            'title'=>'Display Estimated Time',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'If checked, will display the estimated time on the recipe.'
        );
        $keyname = self::SETTINGS_DISPLAY_INSTRUCTIONS;
        $options[]=array(
            'title'=>'Display Instruction List',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'If checked, will display the instruction list and any entered data on the recipe.'
        );
        $keyname = self::SETTINGS_DISPLAY_MEDIA;
        $options[]=array(
            'title'=>'Display Media',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'If checked, will display the media items attached to the recipe.'
        );
        $keyname = self::SETTINGS_MEDIA_SHOW_CAPTIONS;
        $options[]=array(
            'title'=>'Media - Display Captions',
            'objectType'=>WPDEVHUB_DRC_StandardEditor::OT_BOOLEAN,
            'objectName'=>$keyname,
            'formType'=>WPDEVHUB_DRC_StandardEditor::ET_CHECKBOX,
            'value'=>(isset($object->$keyname))?$object->$keyname:true,
            'help'=>'Default setting for new Media attached to a recipe.  If Checked, will display captions associated with media.  Note:  Setting only effects new media objects.'
        );
        return $options;

    }

    /*
    * Attempt to redirect to the base level page for this plugin
    */
    public static function redirectHome(){
        WPDEVHUB_DRC_Utilities::redirect(self::getBaseUrl().'recipe');
    }

    public static function areVirtualPagesEnabled(){
        return WPDEVHUB_DRC_StandardSetting::getBooleanSetting(self::SETTINGS_VP_ENABLED);
    }

    public static function areCategoriesEnabled(){
        // First return a FALSE if virtual pages are disabled
        $vpEnabled = self::areVirtualPagesEnabled();
        if(empty($vpEnabled)){
            return false;
        }
        return WPDEVHUB_DRC_StandardSetting::getBooleanSetting(self::SETTINGS_CATEGORIES_ENABLED);
    }

    public static function areTagsEnabled(){
        // First return a FALSE if virtual pages are disabled
        $vpEnabled = self::areVirtualPagesEnabled();
        if(empty($vpEnabled)){
            return false;
        }
        return WPDEVHUB_DRC_StandardSetting::getBooleanSetting(self::SETTINGS_TAGS_ENABLED);
    }

}
