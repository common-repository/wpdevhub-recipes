<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 3/3/17
 * Time: 4:56 PM
 * To change this template use File | Settings | File Templates.
 */
class WPDEVHUB_DRC_Recipes_Media {

    public $mediaId=0;
    public $displayCaption=true;

    public function __construct(){
        $this->displayCaption = WPDEVHUB_DRC_StandardSetting::getSetting(WPDEVHUB_DRC_Recipes_Main::SETTINGS_MEDIA_SHOW_CAPTIONS);
    }

}
