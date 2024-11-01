<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/3/17
 * Time: 4:56 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_Recipe extends WPDEVHUB_DRC_StandardCustomPostType {

    const KEYNAME = "wpdevhub_drc_recipe";
    const KEYNAME_CATEGORY = "wpdevhub_drc_recipe_category";
    const KEYNAME_TAG = "wpdevhub_drc_recipe_tag";

    const TITLE = 'Recipe';

    const REMOVE_WPAUTOP = false;

    public function __construct(){

        parent::__construct();

    }

    public static function registerTaxonomies(){

        $slugBase = WPDEVHUB_DRC_Recipes_Main::getSlugBase();

        $taxonomies = array();

        $areCategoriesEnabled = WPDEVHUB_DRC_Recipes_Main::areCategoriesEnabled();
        if(!empty($areCategoriesEnabled)){

            // Register the Custom Support Forum Category Structure
            $taxonomies[] = self::KEYNAME_CATEGORY;
            register_taxonomy(self::KEYNAME_CATEGORY, null,
                array(
                    'hierarchical'      => true, // make it hierarchical (like categories)
                    'labels'            => array(
                        'name'              => _x('Recipe Categories', 'taxonomy general name'),
                        'singular_name'     => _x('Recipe Categories', 'taxonomy singular name'),
                        'search_items'      => __('Search Recipe Categories'),
                        'all_items'         => __('All Recipe Categories'),
                        'parent_item'       => __('Parent Recipe Category'),
                        'parent_item_colon' => __('Parent Recipe Category:'),
                        'edit_item'         => __('Edit Recipe Category'),
                        'update_item'       => __('Update Recipe Category'),
                        'add_new_item'      => __('Add New Recipe Category'),
                        'new_item_name'     => __('New Recipe Category Name'),
                        'menu_name'         => __('Categories'),
                    ),
                    'public'            => true,
                    'query_var'         => true,
                    'rewrite'           => ['slug' => $slugBase.'/category', 'hierarchical' => true],
                )
            );

        }

        $areTagsEnabled = WPDEVHUB_DRC_Recipes_Main::areTagsEnabled();
        if(!empty($areTagsEnabled)){

            // Register the Custom Support Forum Category Structure
            $taxonomies[] = self::KEYNAME_TAG;
            register_taxonomy(self::KEYNAME_TAG, null,
                array(
                    'hierarchical'      => false, // make it non hierarchical (like regular tags)
                    'labels'            => array(
                        'name'              => _x('Recipe Tags', 'taxonomy general name'),
                        'singular_name'     => _x('Recipe Tags', 'taxonomy singular name'),
                        'search_items'      => __('Search Recipe Tags'),
                        'all_items'         => __('All Recipe Tags'),
                        'parent_item'       => __('Parent Recipe Tag'),
                        'parent_item_colon' => __('Parent Recipe Tag:'),
                        'edit_item'         => __('Edit Recipe Tag'),
                        'update_item'       => __('Update Recipe Tag'),
                        'add_new_item'      => __('Add New Recipe Tag'),
                        'new_item_name'     => __('New Recipe Tag Name'),
                        'menu_name'         => __('Tags'),
                    ),
                    'public'            => true,
                    'query_var'         => true,
                    'rewrite'           => ['slug' => $slugBase.'/tag'],
                )
            );
        }

        // Register the Custom Recipes Post Type
        $postTypeOptions = array(
            'labels'                => array(
                'name'                  => __( 'Recipes' ),
                'singular_name'         => __( 'Recipe' ),
                'menu_name'             => __( 'Recipes' ),
                'all_items'             => __( 'All Recipes' ),
                'view_item'             => __( 'View Recipe' ),
                'view_items'            => __( 'View Recipes' ),
                'archives'              => __( 'Archive Recipes' ),
                'add_new_item'          => __( 'Add New Recipe' ),
                'edit_item'             => __( 'Edit Recipe' ),
                'update_item'           => __( 'Update Recipe' ),
                'search_items'          => __( 'Search Recipes' ),
            ),
            'description'           => __( 'Dimbal Recipes' ),
            'public'                => false,
            'has_archive'           => false,
            'rewrite'               => false,
            'hierarchical'          => false,
            'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'revisions' ),
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'show_in_admin_bar'     => true,
            'can_export'            => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );

        // See if there were taxonomies to add (they may have been disabled)
        if(!empty($taxonomies)){
            $postTypeOptions['taxonomies'] = $taxonomies;
        }

        // See if the Page Rewrites were enabled
        $arePageRewritesEnabled = WPDEVHUB_DRC_Recipes_Main::areVirtualPagesEnabled();
        if(!empty($arePageRewritesEnabled)){
            $postTypeOptions['public'] = true;
            $postTypeOptions['has_archive'] = true;
            $postTypeOptions['rewrite'] = array('slug' => $slugBase);
        }

        // Now register the post type
        register_post_type( self::KEYNAME,$postTypeOptions);

        // Associate the Taxonomies with the post yypes
        if(!empty($areTagsEnabled)){
            register_taxonomy_for_object_type( self::KEYNAME_TAG , self::KEYNAME );
        }
        if(!empty($areCategoriesEnabled)){
            register_taxonomy_for_object_type( self::KEYNAME_CATEGORY , self::KEYNAME );
        }

    }

    public static function displayFirstImageInArchive(){
        $html = "";
        self::enqueuePublicResources();
        $settingsDisplayMedia = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_MEDIA);
        if($settingsDisplayMedia){
            $mediaObjects = get_post_meta( get_the_ID(), WPDEVHUB_DRC_Recipes_MediaMetaBox::KEYNAME, true);
            if(!empty($mediaObjects) && is_array($mediaObjects)){
                foreach($mediaObjects as $mediaObject){
                    $fullUrl = wp_get_attachment_image_url($mediaObject->mediaId, 'large');
                    $html .= '<div><img id="drc_media_single" class="drc_media_single" src="'.$fullUrl.'" alt="'.$fullUrl.'" /></div>';
                    break;
                }
            }
        }
        return $html;
    }

    public static function get_display($content){

        //WPDEVHUB_DRC_Utilities::logMessage("Inside DRC get_display");

        $postId = get_the_ID();
        $is_single = is_single();

        WPDEVHUB_DRC_Recipes_Main::wpActionEnqueueScripts();

        $html = '';

        // Whether or not to display the Navigation Links and other Meta Fields
        if($is_single){

            try{

                //WPDEVHUB_DRC_Utilities::logMessage("Inside get_display for single recipe");

                // Get the settings associated with displays
                $settingsDisplayMedia = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_MEDIA);
                $settingsDisplayDifficulty = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_DIFFICULTY_ALT);

                $settingsDisplayIngredients = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_INGREDIENTS);
                $settingsDisplayTime = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_TIME);
                $settingsDisplayInstructions = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_INSTRUCTIONS);

                $settingsDisplayIndexReturnTop = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_INDEX_RETURN_TOP);
                $settingsDisplayIndexReturnBottom = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_DISPLAY_INDEX_RETURN_BOTTOM);

                // Top Return link
                if($settingsDisplayIndexReturnTop){
                    $html .= '<div style="text-align:right;"><a href="'.WPDEVHUB_DRC_Recipes_Main::getBaseUrl().'">Back to Recipe List</a></div>';
                }

                $html .= $content;

                // Media
                if($settingsDisplayMedia){
                    $mediaObjects = get_post_meta( get_the_ID(), WPDEVHUB_DRC_Recipes_MediaMetaBox::KEYNAME, true);
                    if(!empty($mediaObjects) && is_array($mediaObjects)){
                        $firstUrl = "";
                        $mediaHtml = "";
                        $mediaHtml .= '<div class="drc_media_thumb_wrapper">';
                        foreach($mediaObjects as $mediaObject){
                            $fullUrl = wp_get_attachment_image_url($mediaObject->mediaId, 'large');
                            if(empty($firstUrl)){
                                $firstUrl = $fullUrl;
                            }
                            $attachment_url = wp_get_attachment_thumb_url($mediaObject->mediaId);
                            if(!empty($attachment_url)){
                                $mediaHtml .= '<img class="drc_media_thumb" onClick="jQuery(\'#drc_media_single\').attr(\'src\',\''.$fullUrl.'\')" src="'.$attachment_url.'" alt="'.$attachment_url.'" />';
                            }
                        }
                        $mediaHtml .= '</div>';

                        $html .= '<div><img id="drc_media_single" class="drc_media_single" src="'.$firstUrl.'" alt="'.$firstUrl.'" /></div>';

                        // Do not include the thumbnails if there is only 1 image
                        if(count($mediaObjects) > 1){
                            $html .= $mediaHtml;
                        }

                    }

                }

                // Space for the next section regardless of whether above was shown or not
                $html .= '<br />';

                $difficultyHtml = "";
                $cooktimeHtml = "";
                $extraInfo = get_post_meta( get_the_ID(), WPDEVHUB_DRC_Recipes_ExtraInfoMetaBox::KEYNAME, true);

                // Difficulty
                if($settingsDisplayDifficulty){
                    if(!empty($extraInfo)){
                        $difficulty = WPDEVHUB_DRC_Recipes_ExtraInfo::getFormattedDifficultyString($extraInfo->difficulty);
                        $difficultyHtml = '<div class="drc_recipe_metabox"><span>Difficulty:</span> '.$difficulty.'</div>';
                    }
                }

                // Cook Time
                if($settingsDisplayTime){
                    if(!empty($extraInfo)){
                        $cooktimeHtml = '<div class="drc_recipe_metabox"><span>Estimated Time:</span> '.$extraInfo->getFinalString().'</div>';
                    }
                }

                // Ingredients
                if($settingsDisplayIngredients){
                    $ingredients = get_post_meta( get_the_ID(), WPDEVHUB_DRC_Recipes_IngredientMetaBox::KEYNAME, true);
                    if(!empty($ingredients) && is_array($ingredients)){
                        $html .= '<div class="drc_subheading"><div style="display:inline-block;">Ingredients</div>'.$difficultyHtml.$cooktimeHtml.'</div>';
                        $html .= '<ul class="drc_generic_list">';
                        foreach($ingredients as $key=>$object){
                            if(is_a($object, 'WPDEVHUB_DRC_Recipes_Ingredient')){
                                $html .= '<li class="drc_generic_item">';
                                if(!empty($object->title)){
                                    $html .= $object->title." : ";
                                }
                                if(!empty($object->quantity)){
                                    $html .= $object->quantity;
                                }
                                if(!empty($object->measurement)){
                                    $measurement = WPDEVHUB_DRC_Recipes_Ingredient::getFormattedMeasurementString($object->measurement);
                                    $html .= " ".$measurement;
                                }

                                $html .= '';
                                $html .= '</li>';
                            }
                        }
                        $html .= '</ul>';
                    }
                }

                // Instructions
                if($settingsDisplayInstructions){
                    $instructions = get_post_meta( get_the_ID(), WPDEVHUB_DRC_Recipes_InstructionMetaBox::KEYNAME , true);
                    if(!empty($instructions) && is_array($instructions)){
                        $html .= '<div class="drc_subheading">Directions</div>';
                        $html .= '<ol class="drc_instruction_list">';
                        foreach($instructions as $key=>$object){
                            if(is_a($object, 'WPDEVHUB_DRC_Recipes_Instruction')){
                                $contents = strtr($object->contents, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />'));
                                $html .= '<li class="drc_instruction_item"><span class="drc_instruction_item_contents">'.$contents.'<span></li>';
                            }
                        }
                        $html .= '</ol>';
                    }
                }

                // Bottom Return link
                if($settingsDisplayIndexReturnBottom){
                    $html .= '<hr />';
                    $html .= '<div style="text-align: right"><a href="'.WPDEVHUB_DRC_Recipes_Main::getBaseUrl().'">Back to Recipe List</a></div>';
                }

                // Display the categories and tags
                $categories = self::getTheTermsDisplay(array('post_id'=>$postId,'taxonomy'=>self::KEYNAME_CATEGORY, 'title'=>''));
                $categoryString = "";
                if(!empty($categories)){
                    $categoryString = "Posted in ".$categories.". ";
                }
                $tags = self::getTheTermsDisplay(array('post_id'=>$postId,'taxonomy'=>self::KEYNAME_TAG, 'title'=>''));
                $tagString = "";
                if(!empty($tags)){
                    $tagString = "Tagged with ".$tags.". ";
                }
                if(!empty($categoryString) || !empty($tagString)){
                    $html .= '<div class="entry-categories">'.$categoryString.$tagString.'</div>';
                }

            }catch(Exception $e){
                error_log("Exception Caught displaying Recipes Guide Steps: ".$e->getMessage());
            }

        }else{

            // Not a single Item - more like a list display

            //WPDEVHUB_DRC_Utilities::logMessage("Inside get_display for list of recipes");

            /*
            $permalink = get_permalink();
            $title = get_the_title($postId);

            $src = null;
            $mediaObjects = get_post_meta( $postId, WPDEVHUB_DRC_Recipes_MediaMetaBox::KEYNAME, true);
            if(!empty($mediaObjects) && is_array($mediaObjects)){
                $mediaObject = array_shift($mediaObjects);
                if($mediaObject){
                    $mediaObject = wp_get_attachment_image_src(WPDEVHUB_DRC_Utilities::getFromObject('mediaId',$mediaObject, 0), 'thumbnail');
                    if ( $mediaObject ) {
                        list($src, $width, $height) = $mediaObject;
                    }
                }
            }


            if(!empty($src)){
                $html .= '
                <div class="drc-div-table">
                    <div class="drc-div-table-cell drc-padding-medium drc-width-150pixel">
                        <a href="'.$permalink.'"><img src="'.$src.'" alt="'.$title.'" /></a>
                    </div>
                    <div class="drc-div-table-cell drc-padding-medium">
                        '.$content.'
                        <br /><br />
                        <div class="drc-align-right"><a href="'.$permalink.'"><button>Read More</button></a></div>
                    </div>
                </div>
            ';
            }else{
                $html .= '
                <div class="drc-div-table">
                    <div class="drc-div-table-cell drc-padding-medium">
                        '.$content.'
                        <br /><br />
                        <div class="drc-align-right"><a href="'.$permalink.'"><button>Read More</button></a></div>
                    </div>
                </div>
                ';
            }
            */


            // Not doing custom Lists for Recipes anymore -- treating them as close to Blog Posts as Possible
            $html = $content;

        }

        return $html;
    }

    public static function getInsertRecipeShortcode($postId){
        return WPDEVHUB_DRC_StandardObjectRecord::buildShortcodeHelper(array('sc_id'=>WPDEVHUB_DRC_Recipes_Main::SC_ID_DISPLAY_RECIPE, 'recipe_id'=>$postId));
    }

    /*
    * This short code handler will display all of the available lists
    */
    public static function shortcodeHandlerDisplayRecipe($atts){

        global $post;

        error_log("Inside shortcodeHandlerDisplayRecipe");

        // Check to see if the Plugin is Active
        if(!WPDEVHUB_DRC_Recipes_Main::isPluginEnabled()){
            WPDEVHUB_DRC_Utilities::logMessage("Inside ShortCode Handler: Plugin is not Enabled");
            return "";
        }

        WPDEVHUB_DRC_Recipes_Main::enqueuePublicResources();

        $html = '';
        $recipe_id = 0;
        extract( shortcode_atts( array(
            'recipe_id' => 0,
        ), $atts ) );


        if(!empty($recipe_id)){

            $post = get_post($recipe_id);
            setup_postdata($post);
            $html = self::theContent();

        }

        return $html;

    }

    public static function wpFilterPostThumbnailHtml( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
        if ( empty( $html ) && empty( $post_thumbnail_id ) ) {
            if(get_post_type() == self::KEYNAME){

                // No thumbnail provided -- find a default
                $mediaObjects = get_post_meta( $post_id, WPDEVHUB_DRC_Recipes_MediaMetaBox::KEYNAME, true);
                if(!empty($mediaObjects) && is_array($mediaObjects)){
                    $mediaObject = array_shift($mediaObjects);
                    if($mediaObject){
                        $html = wp_get_attachment_image(WPDEVHUB_DRC_Utilities::getFromObject('mediaId',$mediaObject, 0), 'thumbnail');
                    }
                }

            }
        }
        return $html;
    }

}
