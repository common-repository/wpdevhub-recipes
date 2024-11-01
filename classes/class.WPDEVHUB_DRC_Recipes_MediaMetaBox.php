<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/21/17
 * Time: 3:46 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_MediaMetaBox extends WPDEVHUB_DRC_StandardMetaBox{

    const KEYNAME = "wpdevhub_drc_media";
    const TITLE = "Recipe Images";
    const SCREEN = WPDEVHUB_DRC_Recipes_Recipe::KEYNAME;

    const ROW_KEY = "drc_media";

    public static function renderCustom( $post ){

        // Use get_post_meta to retrieve an existing value from the database.
        $mediaObjects = self::getPostMeta($post->ID);
        $mediaObjects = WPDEVHUB_DRC_Utilities::ensureIsArray($mediaObjects);

        $html = '';

        // Display the form, using the current value.
        wp_enqueue_media();

        $html .= '<ul id="'.self::ROW_KEY.'_preview" class="WPDEVHUB_DRC_jquery-ui-sortable">';
        if(!empty($mediaObjects)){
            foreach($mediaObjects as $key=>$mediaObject){

                $rowKey = self::ROW_KEY.'_'.$mediaObject->mediaId;
                $wrapperId = $rowKey.'_wrapper';

                // Validate instance type
                if(is_a($mediaObject, 'WPDEVHUB_DRC_Recipes_Media')){

                    $attachment_url = wp_get_attachment_thumb_url($mediaObject->mediaId);
                    if(!empty($attachment_url) && $attachment_url!="(unknown)"){
                        $html .= '<li id="'.$wrapperId.'" class="drc-ml-thumb-wrapper">';
                        $html .= '<div class="drc-align-right"><img src="'.WPDEVHUB_CONST_DRC_URL_IMAGES.'/cancel.png" style="width:14px;" onclick="jQuery(\'#'.$wrapperId.'\').remove();" /></div>';
                        $html .= '<img src="'.$attachment_url.'" width="100" height="100" />';
                        $html .= '<input type="hidden" name="'.self::ROW_KEY.'_mediaId_'.$mediaObject->mediaId.'" id="'.self::ROW_KEY.'_mediaId_'.$mediaObject->mediaId.'" value="'.$mediaObject->mediaId.'" />';
                        $html .= '</li>';
                    }
                }

            }
        }

        $html .= '</ul>';

        $html .= '<input id="media_button" type="button" class="drc-button" value="Open Media Library" onclick="WPDEVHUB_DRC_Admin.openMediaLibraryMultiple(\''.self::ROW_KEY.'\')" />';

        echo $html;

    }

    public static function saveCustom( $post_id ){

        //error_log("MEDIA POST VARS: ".print_r($_POST, true));

        $items = array();
        foreach($_POST as $name=>$value){
            $string = self::ROW_KEY.'_mediaId_';
            if(strpos($name, $string) !== false){
                $media = new WPDEVHUB_DRC_Recipes_Media();
                $media->mediaId = $value;
                $items[]=$media;
            }
        }

        self::savePostMeta($post_id, $items);

    }

}
